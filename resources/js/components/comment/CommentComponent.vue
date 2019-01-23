<template>
    <div>
        <div class="comments">
            <div class="row">
                <div class="col-md-6">
                    <div class="comments-title"><h3>{{ lang.title }} ({{ itemsCount }})</h3></div>
                </div>
                <div style="text-align: right;" class="col-md-6">
                    <select @change="changeOrder()" v-model="order">
                        <option value="DESC">{{ lang.sort.newest }}</option>
                        <option value="ASC">{{ lang.sort.oldest }}</option>
                    </select>
                </div>
            </div>

            <div class="comment-form">
                <comment-form parent-id="0"></comment-form>
            </div>


            <comment-item :items="items" depth="0"></comment-item>

            <button class="btn btn-success" style="width: 100%" v-if="isVisibleMoreButton" @click="getItems()">{{
                lang.buttons.show_more }}
            </button>
        </div>
    </div>
</template>


<script>
    import CommentForm from './forms/comment';
    import CommentItem from './CommentItem';
    import RouteMixin from './mixins/route';
    import ConfigMixin from './mixins/config';
    import ErrorMixin from './mixins/error';
    import bus from './bus';


    export default {
        name: "CommentComponent",
        components: {CommentForm, CommentItem},
        mixins: [RouteMixin, ConfigMixin, ErrorMixin],
        data() {
            return {
                items: [],
                itemsCount: 0,
                page: 1,
                isVisibleMoreButton: false
            };
        },
        created() {
            bus.$on('add-item', (values) => {
                this.addItemToArray(values.parentId, values.item);
            });
        },
        mounted() {
            this.getItemsCount();
            this.getItems();
            console.log(this.lang);
        },
        methods: {
            getItemsCount() {
                axios.post(this.route('count'), {
                    model: this.model,
                    model_id: this.modelId,
                    locale: this.locale
                }).then(response => {
                    if (response.data.success) this.itemsCount = response.data.count;
                }).catch(error => {
                    console.log(error);
                });
            },
            getItems(parentId = null) {
                axios.post(this.route('get'), {
                    model: this.model,
                    model_id: this.modelId,
                    parent_id: parentId,
                    page: this.page,
                    locale: this.locale,
                    order: this.order
                }).then(response => {
                    if (response.data.success) {
                        // Vue.set(this, 'items', response.data.comments);
                        this.items = this.items.concat(response.data.comments);
                        Vue.set(this, 'isVisibleMoreButton', response.data.isVisibleMoreButton);
                        this.page++;
                    }
                }).catch(error => {
                    console.log(error);
                });
            },
            changeOrder() {
                this.items = [];
                this.page = 1;
                this.getItems();
            },
            addItemToArray(parentId, item) {
                if (!item.is_approved) return this.showNotApprovedError();

                this.itemsCount = this.itemsCount + 1;

                if (parentId == 0) {
                    if (this.items.length == this.limit) {
                        console.log('oks');
                        this.items.splice(this.items.length - 1, 1);
                        this.isVisibleMoreButton = true;
                    }
                    this.items.unshift(item);
                } else {
                    insertItem(this.items);

                    function insertItem(array) {
                        for (let key in array) {
                            if (array[key].id == parentId) {
                                array[key].isVisibleForm = false;
                                return array[key].children.unshift(item);
                            }
                            if (array[key].children.length > 0) {
                                insertItem(array[key].children);
                            }
                        }
                    }

                }
            }
        }
    }
</script>

<style scoped>
    .comments {
        margin: 30px 0;
    }

    .comments-title {
        margin-bottom: 15px;
    }


</style>
