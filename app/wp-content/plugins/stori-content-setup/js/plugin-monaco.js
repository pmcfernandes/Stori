jQuery(document).ready(function() {
    alert();

    require.config({ paths: { 'vs': 'https://unpkg.com/monaco-editor@0.8.3/min/vs' }});
    window.MonacoEnvironment = { getWorkerUrl: () => proxy };

    let proxy = URL.createObjectURL(new Blob([`
        self.MonacoEnvironment = {
            baseUrl: 'https://unpkg.com/monaco-editor@0.8.3/min/'
        };
        importScripts('https://unpkg.com/monaco-editor@0.8.3/min/vs/base/worker/workerMain.js');
    `], { type: 'text/javascript' }));

    require(['vs/editor/editor.main'], function() {
        var editor = monaco.editor.create(jQuery('.wp-editor-area').get(0), {
            value: [
                'function x() {',
                '\tconsole.log("Hello world!");',
                '}'
            ].join('\n'),
            language: 'javascript'
        });
    });
});