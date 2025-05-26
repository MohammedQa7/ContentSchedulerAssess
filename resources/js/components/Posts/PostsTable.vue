<template>
    <Card class="mb-6">
        <CardHeader>
            <CardTitle>Posts</CardTitle>
        </CardHeader>
        <CardContent>
            <div v-if="posts.data.length > 0" class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="border-b bg-muted/50">
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Image</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Name</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Content</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Scheduled Time</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Published At</th>
                            <th class="px-4 py-3 text-center font-medium text-muted-foreground">Tags</th>
                            <th class="px-4 py-3 text-center font-medium text-muted-foreground">Platforms</th>
                            <th class="px-4 py-3 text-right font-medium text-muted-foreground">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="post in posts.data" :key="post.id" class="border-b hover:bg-muted/50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-md bg-muted flex items-center justify-center">
                                        <img :src="post.image" :alt="post.title"
                                            class="h-10 w-10 rounded-md object-cover" />
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 text-left">{{ post.title }}</td>
                            <td class="px-4 py-3 text-left">{{ post.content }}</td>
                            <td class="px-4 py-3 text-left">{{ post.scheduledDate ?? 'Not Scheduled' }}</td>
                            <td class="px-4 py-3 text-left">{{ post.publishedAt }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="space-x-1">
                                    <span v-for="(tag, index) in post.tags" :key="index"
                                        class="text-sm text-sky-500 mt-1">
                                        #{{ tag }}
                                    </span>
                                </div>

                            </td>
                            <td class="px-4 py-3 text-center">
                                <Badge v-for="(platform, index) in post.platforms" :key="index">
                                    {{ platform.name }}
                                </Badge>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button variant="ghost" size="icon">
                                            <MoreHorizontalIcon class="h-4 w-4" />
                                            <span class="sr-only">Open menu</span>
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuItem v-if="isNull(post.publishedAt)" @click.prevent="toggleEditPostDialog(post.id)">
                                            <PencilIcon class="mr-2 h-4 w-4" />
                                            Edit
                                        </DropdownMenuItem>
                                        <DropdownMenuSeparator />
                                        <DropdownMenuItem @click.prevent="toggleDeleteDialog(post)"
                                            class=" text-red-500 focus:text-red-500">
                                            <TrashIcon class="mr-2 h-4 w-4" />
                                            Delete
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div v-else
                class="mt-8 flex flex-col items-center justify-center rounded-lg border-2 border-dashed  border-muted-foreground bg-muted p-12 text-center">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-emerald-50">
                    <Package class="h-10 w-10 text-primary-foreground" />
                </div>
                <h3 class="mt-4 text-lg font-medium text-primary">No products found</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating your first product.</p>
            </div>
        </CardContent>
        <CustomPagination :item="posts" />
    </Card>

    <!-- Delete confirmation dialog -->
    <DeleteDialog :is-open="deleteDialogProps.isOpen" @update:open="deleteDialogProps.isOpen = $event"
        :description="'This action will permanently delete the item. Are you sure?'">
        <Button @click.prevent="deletePost" class=" bg-destructive hover:bg-destructive/70">
            Delete
        </Button>
    </DeleteDialog>

    <EditPostDialog v-if="showEditDialog" :is-open="showEditDialog" @update:open="showEditDialog = $event"
        :post="editablePost" :platforms="platforms" :attached-platforms="attachedPlatforms" />
</template>


<script setup>
import { MoreHorizontalIcon, Package, PencilIcon, TrashIcon } from 'lucide-vue-next';
import Card from '../ui/card/Card.vue';
import CardContent from '../ui/card/CardContent.vue';
import CardHeader from '../ui/card/CardHeader.vue';
import CardTitle from '../ui/card/CardTitle.vue';
import Button from '../ui/button/Button.vue';
import Badge from '../ui/badge/Badge.vue';
import DropdownMenu from '../ui/dropdown-menu/DropdownMenu.vue';
import DropdownMenuTrigger from '../ui/dropdown-menu/DropdownMenuTrigger.vue';
import DropdownMenuContent from '../ui/dropdown-menu/DropdownMenuContent.vue';
import DropdownMenuItem from '../ui/dropdown-menu/DropdownMenuItem.vue';
import DropdownMenuSeparator from '../ui/dropdown-menu/DropdownMenuSeparator.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import CustomPagination from '../CustomPagination.vue';
import DeleteDialog from '../DeleteDialog.vue';
import { toast } from 'vue-sonner';
import EditPostDialog from './EditPostDialog.vue';
import {isNull} from 'lodash';
const deleteDialogProps = ref({
    isOpen: false,
    post: null
});
const isLoading = ref(false);
const showEditDialog = ref(false);
const editablePost = ref(null);
const platforms = ref(null);
const attachedPlatforms = ref(null);
const propsData = defineProps({
    posts: Array,
});


const toggleEditPostDialog = (post) => {
    isLoading.value = true;

    router.visit(route('posts.edit', { post: post }), {
        only: ['post', 'attatchedPlatforms', 'unAttatchedPlatforms'],
        preserveScroll: true,
        preserveState: true,
        preserveUrl: true,
        onSuccess: (page) => {
            showEditDialog.value = true;
            editablePost.value = page.props.post.data;
            platforms.value = page.props.unAttatchedPlatforms.data;
            attachedPlatforms.value = page.props.attatchedPlatforms.data;
            isLoading.value = false;
        },
        onError: () => {
            showEditDialog.value = false;
            isLoading.value = false;
        }
    })
}

const toggleDeleteDialog = (post) => {
    deleteDialogProps.value.isOpen = true;
    deleteDialogProps.value.post = post
}

const deletePost = () => {

    if (deleteDialogProps.value.post) {
        router.delete(route('posts.destroy', { post: deleteDialogProps.value.post.id }), {
            onSuccess: () => {
                deleteDialogProps.value.isOpen = false;
                deleteDialogProps.value.post = null;
                toast.success('Post has been deleted');
            },
            onError: () => {
                toast.error('Something went wrong while trying to delete the post.');
            }
        });
    }
}

</script>