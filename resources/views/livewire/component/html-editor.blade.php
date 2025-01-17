<div>
  <div wire:ignore>
    <div id="{{ $fieldId }}" data-model="{{ $model }}" data-listener="{{ $listener }}">{!! $value !!} </div>
  </div>

    @script
    <script>
    (function () {
      let editorTimer = null;
      let textAreaMaxLength = parseInt('{{ $maxLength }}');
      let strictCount = Boolean('{{ $strictCount }}');

      setTimeout(() => {
        let editor = new Quill('#{{ $fieldId }}', {
            modules: {
              toolbar: [
                ['bold', 'italic', 'underline', 'strike', 'blockquote','link',
                { 'list': 'ordered'}, { 'list': 'bullet' },
                { 'header': [1, 2, 3, 4, 5, 6, false] },
                { 'color': [] },
                { 'align': [] }],
              ]
            },
            theme: 'snow',
          });

          var maxChars = 10;
          editor.root.addEventListener('keyup', function(e) {
            e.preventDefault()
            e.stopImmediatePropagation();
            // Perform actions based on the keyup event
          });

          editor.on('text-change', (delta, oldDelta, source) => {
            if (textAreaMaxLength > -1 && getCharLength() > textAreaMaxLength) {
              editor.deleteText(editor.getLength() - 2, 1);
              return;
            }

            emitChange(editor.getSemanticHTML(), editor.container)
          });

          function getCharLength() {
            if (strictCount) {
              return editor.getSemanticHTML().length
            } else {
              return editor.getLength()
            }
          }
      }, 100)

      function emitChange(value, target) {
          if (editorTimer) {
              clearTimeout(editorTimer)
          }

          editorTimer = setTimeout(() => {
            $wire.setValue(value).then(() => {
                running = 0
            })
          }, 600)
      }

    })()
    </script>
    @endscript
</div>