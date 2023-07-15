<template>
    <div :class="{'pb-4': add_padding}" class="d-flex gap-5">
        <div style="max-width: calc(50vw - 3rem);">
            <quill-editor :options="options"  :syntax-highlight="customSyntaxHighlight" placeholder="Front..." contentType="html" v-model:content="notecard.front" theme="snow"></quill-editor>
        </div>
        <div style="max-width: calc(50vw - 3rem);">
            <quill-editor
            :options="options"
            placeholder="Back..." contentType="html" v-model:content="notecard.back" theme="snow"></quill-editor>
        </div>
    </div>
</template>

<script>
import hljs from 'highlight.js'
import 'highlight.js/styles/default.css'; // Import the desired code highlighting style
import 'highlight.js/styles/monokai-sublime.css'

hljs.configure({   // optionally configure hljs
    languages: ['javascript', 'ruby', 'python']
});

export default {
    props: ['notecard', 'add_padding'],
    
    methods: {
        customSyntaxHighlight(code, lang) {
            const highlightedCode = hljs.highlight(lang, code).value;
            return highlightedCode;
        }
    },
    data(){
        return {
            options: {
                debug: 'info',
                modules: {
                    toolbar: [

                        ['bold', 'italic', 'underline', 'code-block'], 
                        [{'list': 'ordered'}]
                        [{ 'header': 1 }, { 'header': 2 }],               // custom button values
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
                    ],
                    syntax: {
                        highlight: text => hljs.highlightAuto(text).value

                    }
                },
                theme: 'snow'
            }
        }

    },
    

};
</script>
