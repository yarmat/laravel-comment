<template>
    <div>
        <template v-for="item in items">

            <div :data-depth="depth" :data-id="item.id" class="comments-tree">
                <div class="d-flex comments-item">
                    <div v-if="item.user.avatar" class="comments-avatar"></div>
                    <div class="w-100 comments-group">
                        <div class="comment-date">
                            <small>{{ item.date }}</small>
                        </div>
                        <div class="comment-author"><b>{{ item.user.name }}</b></div>
                        <div v-html="item.message" class="comment-message"></div>
                        <div class="comment-buttons">
                            <button @click="toggleForm(item)" type="button" class="btn btn-primary">{{
                                lang.buttons.reply }}
                            </button>
                        </div>

                        <comment-form v-if="item.isVisibleForm" :parent-id="item.id"></comment-form>
                    </div>
                </div>

                <comment-item :items="item.children" :depth="parseInt(depth) + 1"></comment-item>

            </div>
        </template>
    </div>
</template>

<script>
    import CommentForm from './forms/comment'
    import ConfigMixin from './mixins/config';

    export default {
        name: "CommentItem",
        props: ['items', 'depth'],
        components: {CommentForm},
        mixins: [ConfigMixin],
        methods: {
            toggleForm(item) {
                item.isVisibleForm = !item.isVisibleForm;
            }
        }
    }
</script>

<style scoped>

    .comments-item {
        margin: 15px 0;
        border: 1px solid navajowhite;
        border-radius: 15px;
        padding: 15px;
    }

    .comments-avatar {
        width: 100px;
        height: 100px;
        border: 1px solid navajowhite;
        border-radius: 15px;
        padding: 15px;
    }

    .comments-group {
        padding-left: 15px;
    }

    .comment-message {
        margin: 10px 0;
    }

    .comments-tree .comments-tree {
        margin-left: 30px;
    }

    .comment-error {
        color: red;
        font-size: 12px;
        margin: 10px 0;
    }
</style>
