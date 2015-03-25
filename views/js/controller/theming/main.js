define([
    'jquery',
    'helpers',
    'lib/farbtastic/farbtastic',
    'ui/feedback',
    'ui/uploader'
],
    function($, helpers, farbtastic, feedback, uploader){

        var theming = {
            start : function(){
                var self = this;

                var $scope = $('.platform-theme-selector');
                var $triggers = $scope.find('.customizer-trigger');
                var $popups = $scope.find('.customizer-popup');
                var cssObject = {};
                var logoObject = {};

                self._initUploader();
                self._initPopups($triggers,$popups, cssObject);

                $('.save').off('click').on('click',function(){
                    self._initConfirmBox($popups, cssObject, logoObject);
                });


                $('.file-uploader').on('upload.uploader',function(e, file,result){
                    logoObject.logo = result;
                    $.ajax({
                        url: helpers._url('getBase64', 'Main', 'taoThemingPlatform'),
                        method: "POST",
                        data: {upload : result.uploaded_file},
                        dataType: "json"
                    }).done(function(response){
                        if(response && response.base64){
                            $('.logo').attr('src',response.base64);

                            $('.icon-close').on('click',function(){
                                $.ajax({
                                    url: helpers._url('getBase64', 'Main', 'taoThemingPlatform'),
                                    method: "POST",
                                    dataType: "json"
                                }).done(function(response){
                                    if(response && response.base64){
                                        $('.logo').attr('src',response.base64);
                                    }
                                });
                            });
                        }
                    });
                });

                $('#link').on('change',function(){
                    $('.logo').closest('a').attr('href', $(this).val());
                    logoObject.link = $(this).val();
                });

                $('#message').on('change',function(){
                    $('.logo').closest('a').attr('title', $(this).val());
                    logoObject.message = $(this).val();
                });


                $('.download-css').on('click', function(){
                    $.ajax({
                        url: helpers._url('download', 'Main', 'taoThemingPlatform'),
                        method: "GET",
                        dataType: "json"
                    }).done(function(response){

                    });
                });

            },

            _initConfirmBox : function($popups, cssObject, logoObject){
                // prompt a confirmation lightbox and then delete the result
                var confirmBox = $('.confirm-modal-feedback'),
                    cancel = confirmBox.find('.cancel'),
                    save = confirmBox.find('.save'),
                    close = confirmBox.find('.modal-close');

                confirmBox.modal({ width: 500 });

                save.off('click')
                    .on('click', function () {
                        $.ajax({
                            url: helpers._url('saveTheme', 'Main', 'taoThemingPlatform'),
                            method: "POST",
                            data: {css:cssObject, logo:logoObject},
                            dataType: "json"
                        }).done(function(response){
                            if(response && response.success){
                                feedback().success(response.message);
                                $popups.not('[style="display: none;"]').hide();
                            }
                            else{
                                feedback().error(response.message);
                            }
                        });
                        confirmBox.modal('close');
                    });

                cancel.off('click')
                    .on('click', function () {
                        confirmBox.modal('close');
                    });

            },

            _initPopups : function($triggers, $popups, cssObject){
                var self = this;

                $triggers.on('click', function(e) {
                    var $elt = $(this),
                        $popup = $($elt.data('popup')),
                        $closer = $('.closer',$popup);

                    e.preventDefault();
                    e.stopPropagation();
                    $popups.not($popup).hide();


                    // close color picker on escape
                    $(document).on('keyup', function(e){
                        if (e.keyCode === 27) {
                            $popup.hide();
                            return false;
                        }
                    });
                    // close color picker on closer click
                    $closer.on('click', function(){
                        $popup.hide();
                    });

                    var $container = $('.color-picker',$popup)

                    $container.each(function(){
                        var $this = $(this);
                        var $input = $($this.find('input'))

                        // event received from modified farbtastic
                        $this.on('colorchange.farbtastic', function (e, color) {

                            var type = ($this.hasClass('background')) ? 'background' : 'color';
                            var local = $elt.data('local') || $elt;
                            var target = $elt.data('target');

                            $(local).each(function(){
                                self._updatePreview($(this), color, type, cssObject);
                            });
                            $input.val(color);
                            color = ($elt.data('important'))?color+' !important':color;

                            var name = $input.attr('name');
                            cssObject[name] = {selector:target, value:color};
                        });
                        self._initializeColorPicker($this, $input);

                    });
                    $popup.show();
                });
            },
            _initUploader : function(){
                var $uploadContainer = $('#upload-stylesheet');
                $uploadContainer.on('upload.uploader', function (e, file, interactionHook) {
                    if(interactionHook.error) {
                        feedback().error(interactionHook.error);
                        return;
                    }

                    feedback().success(interactionHook.success);
                });

                $uploadContainer.on('fail.uploader', function (e, file, interactionHook) {
                    feedback().error(interactionHook.message);
                });

                $uploadContainer.uploader({
                    uploadUrl: $uploadContainer.data('url')
                });
            },

            _initializeColorPicker : function($container,input){
                var color = input.val();
                var widgetObj = $.farbtastic($container);
                widgetObj.setColor(color);

            },

            _updatePreview : function($container,color,type, cssObject){

                $container.css(type, color);
                if(type === 'color' && $container.hasClass('action-bar')){
                    var calculatedColor = $container.css(type).replace('rgb', 'rgba').replace(')', ',.3)');
                    $container.find('.btn-info').css('border-color', calculatedColor);
                    cssObject['action-border-color'] = {selector:$container.data('calculus'), value:calculatedColor};
                }

                if(type === 'background' && $container.hasClass('active')){
                    var calculatedColor = $container.css(type).replace('rgb', 'rgba').replace(')', ',.5)');
                    $('<style>.main-menu li a:hover{background:'+calculatedColor+' !important;}</style>').appendTo('head');
                    cssObject['hover-background'] = {selector:$container.data('calculus'), value:calculatedColor};
                }

            }
        };

        return theming;
    });