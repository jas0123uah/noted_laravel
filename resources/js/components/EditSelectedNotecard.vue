<template>
    <div class="d-flex gap-5">
        <div>
            <quill-editor :toolbar="[

                ['bold', 'italic', 'underline', 'code-block'], 
                [{'list': 'ordered'}]
                [{ 'header': 1 }, { 'header': 2 }],               // custom button values
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
            ]" placeholder="Front..." contentType="html" v-model:content="notecard.front" theme="snow"></quill-editor>
        </div>
        <div>
            <quill-editor
            :toolbar="[

                ['bold', 'italic', 'underline', 'code-block'], 
                [{'list': 'ordered'}]
                [{ 'header': 1 }, { 'header': 2 }],               // custom button values
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
            ]"
            placeholder="Back..." contentType="html" v-model:content="notecard.back" theme="snow"></quill-editor>
        </div>
    </div>
</template>

<script>
export default {
    props: ['notecard'],
};
</script>
