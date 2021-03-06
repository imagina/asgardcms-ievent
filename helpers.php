<?php

use Modules\Ievent\Entities\Category;
use Modules\Ievent\Entities\Event;
use Modules\User\Entities\Sentinel\User;
use Modules\Ievent\Entities\Status;

if (!function_exists('ievent_get_events')) {

    function ievent_get_events($options = array())
    {

        $default_options = array(
            'categoryId' => null,
            'categories' => null,// categoria o categorias que desee llamar, se envia como arreglo ['categories'=>[1,2,3]]
            'users' => null, //usuario o usuarios que desea llamar, se envia como arreglo ['users'=>[1,2,3]]
            'include' => array(),//id de post a para incluir en una consulta, se envia como arreglo ['id'=>[1,2,3]]
            'exclude' => array(),// post, categorias o usuarios, que desee excluir de una consulta metodo de llmado category=>'', posts=>'' , users=>''
            'exclude_categories' => null,// categoria o categorias que desee Excluir, se envia como arreglo ['exclude_categories'=>[1,2,3]]
            'exclude_users' => null, //usuario o usuarios que desea Excluir, se envia como arreglo ['users'=>[1,2,3]]
            'date' =>null, //['from'=>date('Y-m-d H:i:s'), 'to' => date('Y-m-d H:i:s')],
            'take' => 5, //Numero de posts a obtener,
            'skip' => 0, //Omitir Cuantos post a llamar
            'order' => ['field'=>'created_at','way'=>'desc'],//orden de llamado
            //'status' => Status::PUBLISHED,
        );

        $options = array_merge($default_options, $options);

        $event=app('Modules\Ievent\Repositories\EventRepository');
        $params=json_decode(json_encode([
            "filter"=>$options,
            'include'=>['user', 'categories', 'category'],
            'take'=> $options['take'],
            'skip'=> $options['skip'],
            'page'=> isset($options['page']) ? $options['page'] : false
        ]));

        return $event->getItemsBy($params);

    }
}

if (!function_exists('ievent_all_users_by_rol')) {

    function ievent_all_users_by_rol($roleName)
    {

        $users = User::with(['roles'])
            ->whereHas('roles', function ($query) use ($roleName) {
                $query->where('name', '=', $roleName);
            })->latest()->get();

        return $users;

    }
}

if (!function_exists('ievent_get_categories')) {

    function ievent_get_categories($options = array())
    {
        $default_options = array(
            'include' => array(),//id de Categorias  para incluir en una consulta, se envia como arreglo ['include'=>[1,2,3]]
            'exclude' => array(),//id de categorias  que desee excluir de una consulta metodo de llmado category=>'[1,2]'
            'parent' => '0', //id de categorias  padre que desee mostrar en una consulta metodo de llmado category=>'[1,2]'
            'take' => 5, //Numero de posts a obtener,
            'skip' => 0, //Omitir Cuantos post a llamar
            'order' => 'desc',//orden de llamado
        );

        $options = array_merge($default_options, $options);

        $categories = Category::query();
        if (!empty($options['exclude'])) {
            $categories->whereNotIn('id', $options['exclude']);
        }
        if (!empty($options['parent'])) {
            $categories->where('parent_id', $options['parent']);
        }
        $categories->skip($options['skip'])
            ->take($options['take'])
            ->orderBy('created_at', $options['order']);
        if (!empty($options['include'])) {
            $categories->orWhere(function ($query) use ($options) {
                $query->whereIn('id', $options['include']);;
            });
        }
        return $categories->get();

    }
}

if (!function_exists('ievent_format_date')) {
    /**
     * Format date according to local module configuration.
     * @param object $date
     * @param string $format
     *
     * @return string
     **/

    function ievent_format_date($date, $format = '%A, %B %d, %Y')
    {
       
        return strftime($format, strtotime($date));
    }

}

if (!function_exists('ievent_postgallery')) {

    function ievent_postgallery($id)
    {
        $images = Storage::disk('publicmedia')->files('assets/iblog/post/gallery/' . $id);
        return $images;
    }
}

date_default_timezone_set(config('asgard.iblog.config.dateTimezone', 'America/Bogota'));
setlocale(LC_TIME, config('asgard.iblog.config.localeTime', 'en_US.UTF-8'));


if(!function_exists('ievent_get_status')){
    function ievent_get_status($status)
    {
        switch ($status) {
            case trans('iblog::common.status.draft'):
                return Status::DRAFT;
                break;
            case trans('iblog::common.status.pending'):
                return Status::PENDING;
                break;
            case trans('iblog::common.status.published'):
                return Status::PUBLISHED;
                break;
            case trans('iblog::common.status.unpublished'):
                return Status::UNPUBLISHED;
                break;
            default:
                return Status::PUBLISHED;

        }

    }
}