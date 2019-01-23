<script>
    export default {
        name: "errors",
        methods: {
            showErrorsOfValidation(errors) {
                for (let key in errors) {
                    return alert(errors[key][0]);
                }
            },
            showErrorOfTooManyAttempts() {
                alert(this.lang.messages.dont_spam);
            },
            showNotApprovedError() {
                console.log(this.lang.messages);
                alert(this.lang.messages.not_approved);
            }
        }
    }
</script>