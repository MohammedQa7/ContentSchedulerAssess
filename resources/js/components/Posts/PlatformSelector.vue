<template>
    <!-- Platform Selection -->
    <div class="space-y-3">
        <div class="flex justify-between items-center">
            <Label v-if="!isEditable">Publishing Platforms</Label>
            <Label v-else>Pre-assinged Platforms</Label>
            <p class="text-xs text-muted-foreground">Select one or more platforms</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div v-if="!isEditable" v-for="platform in platforms" :key="platform.id"
                @click="togglePlatform(platform.type)" :class="[
                    'cursor-pointer rounded-lg p-3 border transition-all duration-200',
                    isPlatformSelected(platform.type)
                        ? 'border-primary bg-primary/10 shadow-sm'
                        : 'border-border hover:border-primary/20 hover:bg-muted/50'
                ]">
                <div class="flex flex-col items-center text-center">
                    <div class="h-10 w-10 rounded-full flex items-center justify-center mb-2 bg-muted">
                        <component :is="getPlatformIcon(platform.name)" class="h-5 w-5" />
                    </div>
                    <span class="text-sm font-medium">{{ platform.name }}</span>
                    <div class="mt-2">
                        <div class="h-4 w-4 rounded-full mx-auto"
                            :class="isPlatformSelected(platform.type) ? 'bg-primary' : 'bg-muted'"></div>
                    </div>
                </div>
            </div>

            <div v-else v-for="(platform, index) in clonePerSelectedPlatforms" :key="index" :class="[
                ' rounded-lg p-3 border transition-all duration-200',
                isPlatformSelected(platform.type)
                    ? 'border-primary bg-primary/10 shadow-sm'
                    : 'border-border hover:border-primary/20 hover:bg-muted/50'
            ]">
                <div class="flex flex-col items-center text-center">
                    <div class="h-10 w-10 rounded-full flex items-center justify-center mb-2 bg-muted">
                        <component :is="getPlatformIcon(platform.name)" class="h-5 w-5" />
                    </div>
                    <span class="text-sm font-medium">{{ platform.name }}</span>
                    <div class="mt-2">
                        <div @click.prevent="removePlatform(platform.type)"
                            class="w-full p-2 flex items-center rounded border border-destructive bg-destructive/10 cursor-pointer hover:bg-destructive transition-all">
                            <span class="text-xs">Remove</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script setup>
import { computed, ref, watch } from 'vue';
import Label from '../ui/label/Label.vue';
import { Icon, InstagramIcon, LinkedinIcon, Trash, TwitterIcon, X } from 'lucide-vue-next';
import { cloneDeep } from 'lodash';
const emit = defineEmits();
const selectedPlatforms = ref([]);
const removedPlatforms = ref([]);
const propsData = defineProps({
    platforms: Array,
    isEditable: Boolean,
});
const clonePerSelectedPlatforms = ref(cloneDeep(propsData.platforms));

// Assiging icon dpending on platform
const getPlatformIcon = (platform) => {
    switch (platform) {
        case 'Instagram':
            return InstagramIcon;
            break;
        case 'Linkedin':
            return LinkedinIcon;
            break;
        case 'Twitter':
            return TwitterIcon;
            break;
        default:
            return Icon;
            break;
    }
}

// Check if a platform is selected
const isPlatformSelected = (platformType) => {

    return selectedPlatforms.value.includes(platformType);
};

const removePlatform = (platform) => {
    // Remove platform if already selected
    clonePerSelectedPlatforms.value = clonePerSelectedPlatforms.value.filter(item => item.type !== platform);

    emit('bindRemovedPlatforms', platform)
}

// Toggle platform selection
const togglePlatform = (platformType) => {
    if (isPlatformSelected(platformType)) {
        // Remove platform if already selected
        selectedPlatforms.value = selectedPlatforms.value.filter(item => item !== platformType);
    } else {
        // Add platform if not selected
        selectedPlatforms.value.push(platformType);
    }
};

watch(selectedPlatforms, (value) => {
    emit('bindSelectedPlatforms', selectedPlatforms.value)
}, { deep: true })

</script>
<style scoped>
.animate-fadeIn {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
