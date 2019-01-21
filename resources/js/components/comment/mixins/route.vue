<script>
    export default {
        name: "route",
        methods: {
            route(name) {
                return '/' + this.prefix + '/' + name;
            }
        }
    }
</script>
