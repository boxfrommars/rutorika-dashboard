<?php
namespace Rutorika\Dashboard\HTML\Select2;

use Illuminate\Database\Query\Builder;

/**
 * Class Select2ableTrait
 * @traitUses \Rutorika\Dashboard\Controllers\AbstractCrudController
 */
trait Select2ableTrait
{
    protected $_select2titleKey = 'title';

    /**
     * @return Builder
     */
    protected function _getSelect2query()
    {
        $entityClass = $this->_getEntityClass();
        return $entityClass::select('id', $this->_select2titleKey . ' as text');
    }

    public function select2search()
    {
        $titleKey = $this->_select2titleKey;
        $searchTerm = \Input::get('term');
        $query = $this->_getSelect2query();

        if ($searchTerm) {
            $query->where($titleKey, 'LIKE', "%" . $searchTerm . "%");
        }

        return $query->get();
    }

    public function select2searchInit()
    {
        $id = \Input::get('id');
        $isMultiple = \Input::get('multiple');

        if (!$id) \App::abort(404);
        $id = $isMultiple ? (array)explode(',', $id) : $id;

        $entity = $this->_getSelect2query()->find($id);
        return $entity;
    }
}