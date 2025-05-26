<template>
    <!-- <Head :title="'Dashboard'" /> -->
    <CreatePostDialog v-if="isCreatePostDialogOpened" :is-open="isCreatePostDialogOpened" :platforms="platforms"
        @update:open="isCreatePostDialogOpened = $event" />


    <Tabs v-model="currentTab" :default-value="currentTab" class=" m-4">
        <TabsList class="grid w-full grid-cols-2 ">
            <TabsTrigger value="calendar">
                Calendar View
            </TabsTrigger>
            <TabsTrigger value="list">
                List View
            </TabsTrigger>
        </TabsList>
        <div class="text-right my-5">
            <Button @click.prevent="toggleCreatePostDialog">
                <Plus />
                New Post
            </Button>
        </div>
        <TabsContent value="list">
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold">Published Posts</h2>
                </div>

                <!-- Posts -->
                <div class="posts">

                    <div v-for="(post, index) in posts" :key="index">
                        <div>
                            <InstagramPost v-if="post.platform == 'Instagram'" :post="post.post.data" />
                            <TwitterPost v-if="post.platform == 'Twitter'" :post="post.post.data" />
                            <LinkedinPost v-if="post.platform == 'Linkedin'" :post="post.post.data" />
                        </div>
                    </div>
                </div>
            </div>
        </TabsContent>

        <TabsContent value="calendar">
            <Loader2 v-if="isNull(schedualedPosts) || isLoading" class="animate-spin size-8" />

            <PostsCalendar v-else :posts="schedualedPosts" />

        </TabsContent>

    </Tabs>

</template>

<script setup>
import { onMounted, ref, watch } from 'vue'
import {
    Plus,
    Loader2
} from 'lucide-vue-next'
import AppLayout from '@/layouts/AppLayout.vue'
import CreatePostDialog from '@/components/Posts/CreatePostDialog.vue'
import axios from 'axios'
import { toast } from 'vue-sonner'
import Button from '@/components/ui/button/Button.vue'
import InstagramPost from '@/components/Posts/InstagramPost.vue'
import TwitterPost from '@/components/Posts/TwitterPost.vue'
import LinkedinPost from '@/components/Posts/LinkedinPost.vue'
import { Head, router } from '@inertiajs/vue3'
import Tabs from '@/components/ui/tabs/Tabs.vue'
import TabsList from '@/components/ui/tabs/TabsList.vue'
import TabsTrigger from '@/components/ui/tabs/TabsTrigger.vue'
import TabsContent from '@/components/ui/tabs/TabsContent.vue'
import PostsCalendar from '@/components/PostsCalendar.vue'
import { isNull } from 'lodash';
const isCreatePostDialogOpened = ref(false);
const isLoading = ref(false);
const currentTab = ref('list')
const schedualedPosts = ref(null);
const platforms = ref();
const publishedOptions = ref([]);
const urlParams = ref(new URLSearchParams(window.location.search));
const propsData = defineProps({
    posts: Object,
    selectedTab: String
});

const toggleCreatePostDialog = () => {
    axios.get(route('posts.create'))
        .then((response) => {
            platforms.value = response.data.platforms;
            publishedOptions.value = response.data.publishedOptions;
            isCreatePostDialogOpened.value = true;
        })
        .catch(() => {
            isCreatePostDialogOpened.value = false;
            toast.error('Something Went Wrong, Please Try Again Later.');
        })
}

watch(currentTab, (value) => {
    // urlParams.value.set('currentTab', value)
    // window.history.replaceState(null, '', window.location.pathname + '?' + urlParams.value.toString());
    isLoading.value = true;
    if (value == 'calendar') {
        router.visit(route('dashboard', { currentTab: value }), {
            only: ['schedualedPosts'],
            preserveScroll: true,
            preserveState: true,
            onSuccess: (page) => {
                schedualedPosts.value = page.props.schedualedPosts.data;
                isLoading.value = false;
            },
            onError: () => {
                isLoading.value = false;
            }
        })
    } else {
        router.visit(route('dashboard', { _query: '' }));
    }

})

onMounted(() => {
    currentTab.value = propsData.selectedTab ?? 'list'
})
defineOptions({
    layout: AppLayout,
})
</script>