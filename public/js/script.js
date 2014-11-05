var App = {};
/**
 * @param {{Placemark:function, Map:function}} ymaps
 */
var ymaps = ymaps || {};

App.notify = {
    message: function(message, type){
        if ($.isArray(message)) {
            $.each(message, function(i, item){
                App.notify.message(item, type);
            });
        } else {
            $.bootstrapGrowl(message, {
                type: type,
                delay: 4000,
                width: 'auto'
            });
        }
    },

    danger: function(message){
        App.notify.message(message, 'danger');
    },
    success: function(message){
        App.notify.message(message, 'success');
    },
    info: function(message){
        App.notify.message(message, 'info');
    },
    warning: function(message){
        App.notify.message(message, 'warning');
    },
    validationError: function(errors){
        $.each(errors, function(i, fieldErrors){
            App.notify.danger(fieldErrors);
        });
    }
};

$.ajaxSetup({
    headers: {
        'X-CSRF-Token': $('meta[name="_token"]').attr('content')
    }
});


/**
 *
 * @param type string 'insertAfter' or 'insertBefore'
 * @param entityName
 * @param id
 * @param positionId
 */
App.changePosition = function(type, entityName, id, positionId){
    var deferred = $.Deferred();
    $.ajax({
        'url': '/admin/sort',
        'type': 'POST',
        'data': {
            'type': type,
            'entityName': entityName,
            'id': id,
            'positionEntityId': positionId
        },
        /**
         * @param {{errors:[]}} data
         */
        'success': function(data) {
            if (data.success) {
                App.notify.success('Saved!');
            } else {
                App.notify.validationError(data.errors);
            }
        },
        'error': function(){
            App.notify.error('Something wrong!');
        },
        'complete': function(){
            deferred.resolve(true);
        }
    });

    return deferred.promise();
};

/* Sidebar handler */
(function(){
    var $wrapper = $("#wrapper");

    if (window.screen.width <= 768) {
        $wrapper.removeClass("toggled");
    } else {
        $wrapper.toggleClass("toggled", ($.cookie('menu') === ''));
    }

    $('#sidebar-wrapper').find('.nav a').each(function(){
        if (location.href.indexOf($(this).attr('href')) >= 0) {
            $(this).parent('li').addClass('active');
        }
    });

    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $wrapper.toggleClass("toggled");
        $.cookie('menu', $wrapper.hasClass("toggled") ? '' : '1');
    });
})();

if ($('.map').length > 0) {
    ymaps.ready(init);
}


function init(){
    $('.map').each(function(){

        var $container = $(this).parents('.map-container');
        var $input = $container.find('input');
        var val = $input.val();
        var mark;
        var center = [45.035407, 38.975277];
        if (val) {
            center = val.split(':');
            mark = new ymaps.Placemark(center);
        }

        /**
         * @param {{geoObjects, setCenter}} myMap
         */
        var myMap = new ymaps.Map(this, {
            center: center,
            zoom: 14,
            controls: ['smallMapDefaultSet']
        });
        myMap.behaviors.disable('scrollZoom');

        if (mark) {
            myMap.geoObjects.add(mark);
        }

        myMap.events.add('click', function (e) {
            if (mark) myMap.geoObjects.remove(mark);
            var coords = e.get('coords');
            mark = new ymaps.Placemark(coords);
            myMap.geoObjects.add(mark);
            $input.val(coords[0] + ':' + coords[1]);
        });

        $input.on('change', function(){
            var val = $(this).val();
            if (val) {
                if (mark) myMap.geoObjects.remove(mark);
                mark = new ymaps.Placemark(val.split(':'));
                myMap.setCenter(val.split(':'));
                myMap.geoObjects.add(mark);
            }
        });
    });
}

$(document).ready(function(){

    $('.upload-image-container .upload-result').magnificPopup({type:'image'});

    $('.sortable').each(function(){
        var sortType = $(this).data('sorttype') ? $(this).data('sorttype') : 'table';
        var options = {
            update: function(a, b){
                var entityName = $(this).data('entityname');
                var $sorted = b.item;

                var $previous = $sorted.prev();
                var $next = $sorted.next();

                var promise;

                if ($previous.length > 0) {
                    promise = App.changePosition('moveAfter', entityName, $sorted.data('itemid'), $previous.data('itemid'));
                    $.when(promise).done(function(){
                        // do smth
                    });
                } else if ($next.length > 0) {
                    promise = App.changePosition('moveBefore', entityName, $sorted.data('itemid'), $next.data('itemid'));
                    $.when(promise).done(function(){
                        // do smth
                    });
                } else {
                    App.notify.error('Something wrong!');
                }
            },
            cursor: "move"
        };

        if (sortType == 'table') {
            options.handle = '.sortable-handle';
            options.axis = 'y';
        }

        $(this).sortable(options);
    });

    $('.sortable td').each(function(){ // fix jquery ui sortable table row width issue
        $(this).css('width', $(this).width() +'px');
    });

    $('.js-color-field').minicolors({
        theme: 'bootstrap'
    });

    $('body').on('click', '[data-action="destroy"]', function(e){
        if (!confirm('Вы действительно хотите удалить?')) {
            e.preventDefault();
        }
    });

    $('.js-date-field').datetimepicker({
        pickTime: false,
        language: 'ru'
    });

    $('.js-datetime-field').datetimepicker({
        language: 'ru'
    });

    $('.js-time-field').datetimepicker({
        pickDate: false,
        language: 'ru'
    });
});

(function($){
    $(document).ready(function(){
        $('.js-uploader').each(function(){
            $(this).fileupload({
                dataType: 'json',
                url: $(this).data('url'),
                paramName: 'file',
                formData: [{
                    name: 'type',
                    value: $(this).data('type')
                }],

                done: function (e, data) {
                    /**
                     * @type {{path:string, filename:string}} result
                     */
                    var result = data.result;
                    if (result.success === false) {
                        App.notify.validationError(result.errors);
                    } else {
                        App.notify.success('Файл загружен успешно');
                        var $container = $(this).parents('.upload-container');
                        $container.find('.uploader-input').val(result.filename);
                        $container.find('.upload-result').attr('href', result.path);
                        $container.trigger('uploaded', [result]);
                    }
                }
            })
        });

        $('.js-upload-remove').on('click', function(e){
            e.preventDefault();
            var $container = $(this).parents('.upload-container');
            $container.find('.uploader-input').val('');
            $container.find('.upload-result').attr('href', '');
            $container.trigger('removed', [$container]);
        });

        $('.upload-image-container')
            .on('uploaded', function(e, result){
                $(this).find('.upload-result img').attr('src', result.path);
            })
            .on('removed', function(){
                $(this).find('.upload-result img').attr('src', '');
            });

        $('.upload-file-container')
            .on('uploaded', function(e, result){
                $(this).find('.upload-result').text(result.filename);
            })
            .on('removed', function(){
                $(this).find('.upload-result').text('');
            });
    });
})(jQuery);