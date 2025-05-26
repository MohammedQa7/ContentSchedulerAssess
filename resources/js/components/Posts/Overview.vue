<template>
    <div class="space-y-6">
        <!-- Scheduled vs Published -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="p-6 pb-2">
                <h3 class="text-lg font-semibold">Scheduled vs Published</h3>
                <p class="text-sm text-muted-foreground">Content status breakdown by platform</p>
            </div>
            <div class="p-6 pt-0">
                <div class="space-y-6">
                    <div v-for="platform in postCountPerPlatform" :key="platform.name" class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="font-medium">{{ platform.name }}</span>
                            <span class="text-sm text-muted-foreground">{{ platform.postsCount }} total</span>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div class="text-center p-3 rounded-lg bg-green-50 border border-green-200">
                                <div class="text-lg font-semibold text-green-700">{{ platform.publishedPostsCount }}
                                </div>
                                <div class="text-xs text-green-600">Published</div>
                            </div>
                            <div class="text-center p-3 rounded-lg bg-orange-50 border border-orange-200">
                                <div class="text-lg font-semibold text-orange-700">{{ platform.scheduledPostsCount }}
                                </div>
                                <div class="text-xs text-orange-600">Scheduled</div>
                            </div>
                        </div>

                        <!-- Progress bars -->
                        <div class="flex space-x-1 h-2">
                            <div class="bg-green-500 rounded-l"
                                :style="{ width: `${(platform.publishedPostsCount / platform.postsCount) * 100}%` }">
                            </div>
                            <div class="bg-orange-500 rounded-r"
                                :style="{ width: `${(platform.scheduledPostsCount / platform.postsCount) * 100}%` }">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script setup>
const propsData = defineProps({
    postCountPerPlatform: Array,
})
console.log(propsData);

</script>

<style scoped>
.transition-all {
    transition: all 0.3s ease;
}
</style>