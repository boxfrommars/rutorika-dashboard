<?php

namespace Rutorika\Dashboard\HTML;

use Illuminate\Html\FormBuilder as IlluminateFormBuilder;

class FormBuilder extends IlluminateFormBuilder
{
    /**
     * @param string $title
     * @param string $name
     * @param mixed $value
     * @param array $fieldOptions
     * @param array $options
     * @return string
     */
    public function textField($title, $name, $value = null, $fieldOptions = [], $options = [])
    {
        $fieldOptions = $this->formFieldDefaultOptions($name, $fieldOptions);
        $field = $this->text($name, $value, $fieldOptions);

        return $this->formRow($title, $name, $field, $options);
    }

    /**
     * @param string $title
     * @param string $name
     * @param mixed $value
     * @param array $fieldOptions
     * @param array $options
     * @return string
     */
    public function textareaField($title, $name, $value = null, $fieldOptions = [], $options = [])
    {
        $fieldOptions = array_merge(['rows' => 4], $this->formFieldDefaultOptions($name, $fieldOptions));
        $field = $this->textarea($name, $value, $fieldOptions);

        return $this->formRow($title, $name, $field, $options);
    }

    /**
     * @param string $title
     * @param string $name
     * @param null $value
     * @param array $fieldOptions
     * @param array $options
     * @return string
     */
    public function colorField($title, $name, $value = null, $fieldOptions = [], $options = [])
    {
        $fieldOptions = $this->formFieldDefaultOptions($name, $fieldOptions);
        $field = $this->color($name, $value, $fieldOptions);

        return $this->formRow($title, $name, $field, $options);
    }

    /**
     * @param string $title
     * @param string $name
     * @param mixed $value
     * @param array $fieldOptions
     * @param array $options
     * @return string
     */
    public function numberField($title, $name, $value = null, $fieldOptions = [], $options = [])
    {
        $fieldOptions = $this->formFieldDefaultOptions($name, $fieldOptions);
        $field = $this->number($name, $value, $fieldOptions);

        return $this->formRow($title, $name, $field, $options);
    }

    public function selectField($title, $name, $list = [], $selected = null, $fieldOptions = [], $options = [])
    {
        $fieldOptions = $this->formFieldDefaultOptions($name, $fieldOptions);
        $field = $this->select($name, $list, $selected, $fieldOptions);

        return $this->formRow($title, $name, $field, $options);
    }

    public function checkboxField($title, $name, $value = 1, $checked = null, $fieldOptions = [], $options = [])
    {
        $field = '<div class="checkbox"><label>' . $this->checkbox($name, $value, $checked, $fieldOptions) . '</label></div>';

        return $this->formRow($title, $name, $field, $options);
    }

    public function geopointField($title, $name, $value = null, $fieldOptions = [], $options = [])
    {
        $fieldOptions = $this->formFieldDefaultOptions($name, $fieldOptions);
        $field = $this->geopoint($name, $value, $fieldOptions);

        return $this->formRow($title, $name, $field, $options);
    }

    public function imageField($title, $name, $value = null, $fieldOptions = [], $options = [])
    {
        $fieldOptions = $this->formFieldDefaultOptions($name, $fieldOptions);
        $options = array_merge(['type' => 'default'], $options);

        $field = $this->uploadField('image', $name, $value, $fieldOptions, $options);

        return $this->formRow($title, $name, $field, $options);
    }

    public function fileField($title, $name, $value = null, $fieldOptions = [], $options = [])
    {
        $fieldOptions = $this->formFieldDefaultOptions($name, $fieldOptions);
        $options = array_merge(['type' => 'default-file'], $options);

        $field = $this->uploadField('file', $name, $value, $fieldOptions, $options);

        return $this->formRow($title, $name, $field, $options);
    }

    /**
     * @param string $uploadType image|file
     * @param string $name
     * @param null $value
     * @param array $fieldOptions
     * @param array $options
     * @return string
     */
    public function uploadField($uploadType = 'image', $name, $value = null, $fieldOptions, $options)
    {
        $type = $options['type'];

        $src = $value ? "/assets/{$uploadType}/{$type}/{$value}" : null;
        $uploadUrl = isset($options['url']) ? $options['url'] : '/upload';
        $fieldOptions = $this->_appendClassToOptions('hidden uploader-input', $fieldOptions);

        $uploadResultHtml = $uploadType === 'image' ? '<img class="media-object" src="' . $src . '" />' : $value;

        return '<div class="media upload-container upload-' . $uploadType . '-container">
                    <span class="pull-left">
                        <a class="upload-result" href="' . $src . '">' . $uploadResultHtml . '</a>
                        ' . $this->text($name, $value, $fieldOptions) . '
                    </span>
                    <div class="media-body">
                        <span class="btn btn-default btn-xs fileinput-button">
                            <i class="glyphicon glyphicon-picture"></i>
                            <span></span>
                            <input type="file" class="js-uploader" data-type="' . $type . '" data-url="' . $uploadUrl . '">
                        </span>
                        <a href="#" class="btn btn-default btn-xs js-upload-remove" title="Удалить"><i class="glyphicon glyphicon-remove"></i></a>
                    </div>
                </div>';
    }

    public function dateField($title, $name, $value = null, $fieldOptions = [], $options = [])
    {
        $fieldOptions = $this->formFieldDefaultOptions($name, $fieldOptions);
        $field = $this->datetime($name, $value, $fieldOptions, 'date');

        return $this->formRow($title, $name, $field, $options);
    }

    public function datetimeField($title, $name, $value = null, $fieldOptions = [], $options = [])
    {
        $fieldOptions = $this->formFieldDefaultOptions($name, $fieldOptions);
        $field = $this->datetime($name, $value, $fieldOptions, 'datetime');

        return $this->formRow($title, $name, $field, $options);
    }

    public function timeField($title, $name, $value = null, $fieldOptions = [], $options = [])
    {
        $fieldOptions = $this->formFieldDefaultOptions($name, $fieldOptions);
        $field = $this->datetime($name, $value, $fieldOptions, 'time');

        return $this->formRow($title, $name, $field, $options);
    }

    public function datetime($name, $value = null, $fieldOptions = [], $type = 'date')
    {
        switch ($type) {
            case 'datetime':
                $typeClass = 'js-datetime-field';
                break;
            case 'time':
                $typeClass = 'js-time-field';
                break;
            case 'date':
                // no break
            default:
                $typeClass = 'js-date-field';
        }

        return '<div class="input-group date ' . $typeClass . '">' .
        $this->text($name, $value, $fieldOptions) .
        '<span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>';
    }

    public function geopoint($name, $value = null, $options = [])
    {
        $html = '<div class="map-container">';
        $html .= '<div class="map" style="width: 100%; height:400px"></div>';
        $html .= $this->text($name, $value, $options);
        $html .= '</div>';

        return $html;
    }

    /**
     * @param string $title
     * @return string
     */
    public function submitField($title = 'Сохранить')
    {
        return '
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-9">
                    <button type="submit" class="btn btn-primary">' . $title . '</button>
                </div>
            </div>';
    }

    /**
     * @param string $href
     * @param string $title
     * @return string
     */
    public function formAddAnotherField($href, $title = 'Добавить ещё')
    {
        return '
        <div class="row">
            <div class="col-sm-offset-3 col-sm-9">
                <a href="' . $href . '" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span> ' . $title . '</a>
            </div>
        </div>';
    }

    /**
     * @param string $title
     * @param string $name
     * @param string $field
     * @param array $options
     * @return string
     */
    public function formRow($title, $name, $field, $options)
    {
        $html = '';
        $html .= '<div class="form-group">';
        $html .= $this->label($name, $title, ['class' => 'col-sm-3 control-label']);

        $html .= '<div class="col-sm-9">';
        $html .= $field;
        $html .= array_key_exists('help', $options) ? '<span class="help-block">' . $options['help'] . '</span>' : '';

        if (isset($this->session)) {
            $errors = $this->session->get('errors');
            if ($errors) {
                $html .= $errors->first($name, '<p class="text-danger">:message</p>');
            }
        }

        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * @param string $name
     * @param array $options
     * @return array
     */
    public function formFieldDefaultOptions($name, $options)
    {
        $options = $this->_appendClassToOptions('form-control', $options);

        if (isset($this->session)) {
            $errors = $this->session->get('errors');
            if ($errors && $errors->first($name)) {
                $options = $this->_appendClassToOptions('has-error', $options);
            }
        }

        return $options;
    }

    public function color($name, $value = null, $options = [])
    {
        $options = $this->_appendClassToOptions('js-color-field', $options);

        return $this->text($name, $value, $options);
    }

    protected function _appendClassToOptions($class, array $options = [])
    {
        $options['class'] = isset($options['class']) ? $options['class'] . ' ' : '';
        $options['class'] .= $class;

        return $options;
    }
}