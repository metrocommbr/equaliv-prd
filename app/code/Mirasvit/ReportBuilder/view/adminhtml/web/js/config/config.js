define([
    'jquery',
    'Magento_Ui/js/form/element/textarea',
    'Mirasvit_ReportBuilder/js/codemirror/codemirror'
], function ($, Textarea, CodeMirror) {
    'use strict';

    return Textarea.extend({
        afterRender: function() {
            var self = this;
            var element = document.getElementById(this.uid);

            var editor = CodeMirror.fromTextArea(element, {
                mode: 'text/html',
                tabMode: 'indent',
                matchTags: true,
                viewportMargin: Infinity,
                styleActiveLine: true,
                tabSize: 2,
                lineNumbers: true,
                lineWrapping: false
            });

            editor.on('change', function (instance, changeObj) {
                // update editor and element values
                editor.refresh();
                editor.save();

                var field = $(editor.getTextArea());

                // update value in UI component for successful save on server side
                self.value(field.val());
            });
        }
    });
});
