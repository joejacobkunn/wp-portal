<div>
    <div wire:ignore>
    <trix-editor id="{{ $fieldId }}" style="min-height: {{ $height }}px !important; overflow: auto; max-height: {{ $maxHeight }}px;"></trix-editor>
    </div>
@assets
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.js"></script>
@endassets

@script
<script>
(function () {
    var editorInits = editorInits || []
    var editorTimer = editorTimer || null
    var styles = Array
        .from(document.styleSheets)
        .map(scr => scr.src);

    var stylePath = 'https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.css'
    if (!styles.includes(stylePath)) {
        var link = document.createElement("link");
        link.rel = "stylesheet";
        link.href = stylePath;
        document.getElementsByTagName("head")[0].appendChild(link);
    }

    var scripts = Array
        .from(document.querySelectorAll('script'))
        .map(scr => scr.src);
    var scriptPath = 'https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.js'
    if (!scripts.includes(scriptPath)) {
        let tag = document.createElement('script');
        tag.src = scriptPath;
        document.getElementsByTagName('body')[0].appendChild(tag);
    }

    let running = 0;
    function emitChange(target) {
        if (editorTimer) {
            clearTimeout(editorTimer)
        }

        if (running) {
            return;
        }

        editorTimer = setTimeout(() => {
            running = 1
            $wire.setValue(target.value).then(() => {
                running = 0
            })
           //window.livewire.dispatch('fieldUpdated', '{{ $model }}' , e.target.value)
        }, 600)
    }

    document.addEventListener('trix-initialize', function (e) {
        if (!editorInits.includes(e.target.getAttribute('id'))) {
            editorInits.push(e.target.getAttribute('id'))
            e.target.editor.insertHTML('{!! str_replace("\n", "", nl2br($value)) !!}')
            afterEditorInit()
        }
    })

    function afterEditorInit() {
        document.addEventListener('trix-change', function (e) {
            emitChange(e.target)
        })

        //disabled image paste for now
        document.addEventListener('trix-attachment-add', function (e) {
            e.preventDefault()
            e.attachment.remove()
        })
    }
    });
</script>
@endscript
</div>
