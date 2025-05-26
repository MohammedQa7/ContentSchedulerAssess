<template>
    <div class="p-4 space-y-6">
        <header class="mb-8">
            <div class="flex items-center space-x-2 mb-2">
                <div class="size-8 rounded-md bg-primary flex items-center justify-center">
                    <Signpost class="size-4 text-primary-foreground" />
                </div>
                <h1 class="text-3xl font-bold tracking-tight">Posts Manager</h1>
            </div>
            <p class="text-muted-foreground">Manage all of your posts.</p>
        </header>

        <!-- Posts overview -->
        <Overview v-if="postCountPerPlatform" :post-count-per-platform="postCountPerPlatform.data" />

        <!-- Post filitring  -->
        <Filter :publish-status="publishStatus" />

        <!-- Posts table -->
        <PostsTable :posts="posts" :platforms="platforms" />
    </div>

</template>

<script setup>
import CreatePostDialog from '@/components/Posts/CreatePostDialog.vue';
import Filter from '@/components/Posts/Filter.vue';
import Overview from '@/components/Posts/Overview.vue';
import PostsTable from '@/components/Posts/PostsTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Signpost } from 'lucide-vue-next';
const propsData = defineProps({
    posts: Object,
    platforms: Object,
    publishStatus: Array,
    schedualedPosts: Object,
    postCountPerPlatform: Array,
})

defineOptions({
    layout: AppLayout,
});
</script>