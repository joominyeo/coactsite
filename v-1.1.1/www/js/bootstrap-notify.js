/**
 * bootstrap-notify.js v1.0
 * --
  * Copyright 2012 Goodybag, Inc.
 * --
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * 
 * https://github.com/goodybag/bootstrap-notify
 */

(function ($) {
    var Notification = function (element, options) {
        // Element collection
        this.$element = $(element);
        this.$note    = $('<div class="alert"></div>');
        this.options  = $.extend(true, {}, $.fn.notify.defaults, options);

        // Setup from options
        if(this.options.transition)
            if(this.options.transition == 'fade')
                this.$note.addClass('in').addClass(this.options.transition);
            else this.$note.addClass(this.options.transition);
        else this.$note.addClass('fade').addClass('in');

        if(!this.options.type){
            if(this.$element.data("type") !== '')
                this.$note.addClass('alert-' + this.$element.data("type"));
            else 
                this.$note.addClass('alert-success');//default value
        }else
            this.$note.addClass('alert-' + this.options.type);        

        if(!this.options.message && this.$element.data("message") !== '') // dom text
            this.$note.html(this.$element.data("message"));
        else{
            if(typeof this.options.message === 'object'){
                if(this.options.message.html)
                    this.$note.html(this.options.message.html);
                else if(this.options.message.text)
                    this.$note.text(this.options.message.text);
                else
                    this.$note.html(this.options.message);
            }
        }
        //check if closable set in html data attributes
        if(!this.options.closable) {
            if (this.$element.data("closable") !== '')
                this.options.closable=this.$element.data("closable"); 
            else
                this.options.closable=true;//default true
        }
        
        if(!this.options.fadeOut.enabled){
            if(this.$element.data("fadeout") !== '')
                this.options.fadeOut.enabled=this.$element.data("fadeout");
            else
                this.options.fadeOut.enabled=true;
        }

        if(this.options.closable)
            var link = $('<a class="close pull-right" href="#">&times;</a>');
        $(link).on('click', $.proxy(onClose, this));
        this.$note.prepend(link);

        return this;
    };

    var onClose = function() {
        this.options.onClose();
        $(this.$note).remove();
        this.options.onClosed();
        return false;
    };

    Notification.prototype.show = function () {
        if(this.options.fadeOut.enabled)
            this.$note.delay(this.options.fadeOut.delay || 3000).fadeOut('slow', $.proxy(onClose, this));

        this.$element.append(this.$note);
        this.$note.alert();
    };

    Notification.prototype.hide = function () {
        if(this.options.fadeOut.enabled)
            this.$note.delay(this.options.fadeOut.delay || 3000).fadeOut('slow', $.proxy(onClose, this));
        else onClose.call(this);
    };

    $.fn.notify = function (options) {
        return new Notification(this, options);
    };

    $.fn.notify.defaults = {
        type: null,
        closable: null,
        transition: 'fade',
        fadeOut: {
            enabled: null,
            delay: 5000
        },
        message: null,
        onClose: function () {},
        onClosed: function () {}
    }
})(window.jQuery);
