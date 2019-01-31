jQuery(document).ready(function () {
    require.config({
        paths: { 'vs': 'https://unpkg.com/monaco-editor@0.15.6/min/vs' }
    });

    let proxy = URL.createObjectURL(new Blob([`
        self.MonacoEnvironment = {
            baseUrl: 'https://unpkg.com/monaco-editor@0.15.6/min/'
        };
        importScripts('https://unpkg.com/monaco-editor@0.15.6/min/vs/base/worker/workerMain.js');
    `], { type: 'text/javascript' }));

    window.MonacoEnvironment = { getWorkerUrl: () => proxy };
    window.currentContentValue = document.getElementById('content').value;

    window.startMonitor = () => {
        window.monitorChanges = setInterval(() => {
            var oldValue = window.currentContentValue;
            var newValue = document.getElementById('content').value;
            
            if (oldValue != newValue) {
                var value = document.getElementById('content').value;                
                // window.editor.setValue(value);
                window.currentContentValue = newValue;
            }
        }, 250);
    };

    window.stopMonitor = () => {
        clearInterval(window.monitorChanges);
    };

    require(['vs/editor/editor.main'], function () {
        var contentElement = document.getElementById('content');
        contentElement.style.position = 'fixed';
        contentElement.style.left = '-9999px';

        var monacoEditorElement = document.createElement('div');
        monacoEditorElement.id = 'monaco-editor';
        monacoEditorElement.style = 'height: 600px; margin-top: 0px;';

        contentElement.parentNode.insertBefore(monacoEditorElement, contentElement.nextSibling);

        window.editor = monaco.editor.create(monacoEditorElement, {
            value: contentElement.value,
            language: 'html',
            lineNumbers: true,
            roundedSelection: false,
            scrollBeyondLastLine: false,
            readOnly: false,
            theme: "vs-dark",
            wordWrap: 'wordWrapColumn',
            wordWrapColumn: 120,
            wordWrapMinified: true,
            wrappingIndent: "indent",
        });

        window.editor.onKeyUp(() => {
            var value = window.editor.getValue();
            window.stopMonitor();
            document.getElementById('content').value = value;
            $('.word-count').html(value.length);
            window.startMonitor();
        });
    
        window.startMonitor();
    });
});

(function ($) {
    $('#wp-content-editor-tools, #ed_toolbar').hide();

    $('#content-tmce').on('click', function() {
        $('#monaco-editor').hide();
    });

    $('#content-html').on('click', function() {
        $('#monaco-editor').show();
    });
})(jQuery);