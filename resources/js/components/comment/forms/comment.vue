<template>
    <div>
        <form @submit.prevent="store" class="comment-form">
            <div v-if="! isUserLogged" class="form-group">
                <input type="text" v-model="name" class="form-control" placeholder="Имя">
            </div>
            <div v-if="! isUserLogged" class="form-group">
                <input type="email" v-model="email" class="form-control" placeholder="E-mail">
            </div>
            <div class="form-group">
                <textarea class="form-control" v-model="message" rows="3"></textarea>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">{{ lang.buttons.send }}</button>
            </div>
        </form>
    </div>
</template>


<script>
    import RouteMixin from './../mixins/route';
    import ConfigMixin from './../mixins/config';
    import ErrorMixin from './../mixins/error';
    import bus from './../bus';

    export default {
        name: "CommentForm",
        props: ['parent-id'],
        mixins: [RouteMixin, ConfigMixin, ErrorMixin],
        data() {
            return {
                message: ''
            }
        },
        methods: {
            store() {
                // console.log(this.model, this.modelId, this.parentId, this.prefix, this.isUserLogged);
                axios.post(this.route('store'), {
                    name: this.name,
                    email: this.email,
                    message: this.message,
                    parent_id: this.parentId,
                    model: this.model,
                    model_id: this.modelId,
                    locale: this.locale
                }).then(response => {
                    if (response.data.success) {
                        this.message = '';
                        bus.$emit('add-item', {
                            parentId: this.parentId,
                            item: response.data.comment
                        });

                        this.updateConfigAuthor(this.name, this.email);
                    }
                }).catch(error => {

                    if (error.response.status === 422) this.showErrorsOfValidation(error.response.data.errors)

                    if (error.response.status === 429) this.showErrorOfTooManyAttempts();
                });

            }
        }
    }
</script>

<style scoped>
    .comment-form {
        margin: 15px 0;
    }
</style>
