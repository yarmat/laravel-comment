<script>
    export default {
        data() {
            return {
                name: '',
                email: '',
                order: '',
                locale: '',
                prefix: '',
                modelId: '',
                model: '',
                isUserLogged: false,
                lang: ''
            }
        },
        created() {
            this.name = window.Comment.name;
            this.email = window.Comment.email;
            this.order = window.Comment.order;
            this.locale = window.Comment.locale;
            this.prefix = window.Comment.prefix;
            this.model = window.Comment.model;
            this.modelId = window.Comment.model_id;
            this.isUserLogged = window.Comment.isUserLogged;
            this.lang = window.Comment.lang;
        },
        methods: {
            updateConfigAuthor(name, email) {
                window.Comment.name = name;
                window.Comment.email = email;
            }
        }
    }
</script>
