<template>
    <Dialog v-model:open="isDialogOpened" @update:open="isDialogOpened = $event">
        <DialogContent :hide-close-button="true" class="sm:max-w-[525px] max-h-[80%] overflow-x-auto">
            <DialogHeader>
                <DialogTitle>New Event</DialogTitle>
            </DialogHeader>

            <!-- Title Input -->
            <form class="space-y-8 grid">
                <div class="space-y-2">
                    <div>
                        <Label for="title">Post Title</Label>
                    </div>
                    <Input id="title" v-model="form.title" placeholder="Enter post title" required />
                    <InputError :message="form.errors?.title" />
                </div>

                <!-- Content Input -->
                <div class="space-y-2">
                    <div>
                        <Label for="content">Post Content</Label>
                    </div>
                    <Textarea id="content" v-model="form.content" rows="6" placeholder="Write your post content here..."
                        required />
                    <InputError :message="form.errors?.content" />
                    <p class="text-sm text-muted-foreground text-right">{{ form.content.length }}</p>
                </div>

                <!-- Platform Selection -->
                <PlatformSelector @bindSelectedPlatforms="bindSelectedPlatforms" :platforms="platforms" />
                <InputError :message="form.errors?.platforms" />

                <!-- Publishing Options with RadioGroup -->
                <PublishingOptions @bindSelectedOption=bindSelectedPublishingOption :selected-option="'published'" />
                <InputError :message="form.errors?.publishOption" />


                <!-- Schedule Date/Time (conditionally shown) -->
                <div v-if="form.publishOption == 'scheduled'"
                    class="p-5 bg-muted/50 rounded-lg border border-border animate-fadeIn space-y-4">
                    <h4 class="text-sm font-medium">Schedule Details</h4>
                    <div class="space-y-2 flex flex-col">
                        <Label for="start-date" class="text-sm font-medium">Date</Label>
                        <Calendar @bindCalendarDate="bindCalendarDate" />
                        <InputError :message="form.errors?.scheduledDate" />
                    </div>

                    <div class="  space-y-2 flex flex-col">
                        <Label for="start-date" class="text-sm font-medium">Specific time</Label>
                        <TimePicker with-period with-labels v-model:date="startsAt" />
                    </div>
                </div>

                <!-- Optional Image -->
                <div class="grid gap-2 space-y-2">
                    <!-- FilePond -->
                    <Label for="security-level">Image</Label>
                    <file-pond name="attachments" ref="filepond" class-name="my-pond" v-bind:allow-multiple="false"
                        accepted-file-types="image/*" label-idle="Drop Or Click to Upload All attachments"
                        v-bind:files="myFiles" v-on:init="handleFilePondInit" :server="{
                            url: '',
                            process: {
                                url: route('upload'),
                                method: 'post',
                                onload: handleFilePondLoad,
                            },
                            revert: handleFilePondRevert,
                            headers: {
                                'X-CSRF-TOKEN': page.props.csrf
                            }
                        }" />
                    <InputError :message="form.errors?.image" />
                </div>

                <!-- Tags (optional feature) -->
                <div class="space-y-2">
                    <div>
                        <Label for="tags">Tags (optional)</Label>
                    </div>
                    <TagsInput v-model="form.tags">
                        <TagsInputItem v-for="item in form.tags" :key="item" :value="item">
                            <TagsInputItemText />
                            <TagsInputItemDelete />
                        </TagsInputItem>

                        <TagsInputInput placeholder="Hashtags..." />
                    </TagsInput>
                </div>

                <!-- Submit Button -->
                <div class=" flex justify-end pt-2 space-x-2">
                    <DialogClose>
                        <Button variant="outline">
                            Close
                        </Button>
                    </DialogClose>
                    <Button @click.prevent="submit" :disabled="form.processing">
                        {{ form.processing ? 'Creating ...' : submitButtonText }}
                    </Button>
                </div>
            </form>

        </DialogContent>

    </Dialog>

</template>

<script setup>
import { computed, ref, watch } from 'vue';
import Dialog from '../ui/dialog/Dialog.vue';
import DialogContent from '../ui/dialog/DialogContent.vue';
import DialogHeader from '../ui/dialog/DialogHeader.vue';
import DialogTitle from '../ui/dialog/DialogTitle.vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import Button from '../ui/button/Button.vue';
import Calendar from '../Calendar.vue';
import Label from '../ui/label/Label.vue';
import Input from '../ui/input/Input.vue';
import InputError from '../InputError.vue';
import TimePicker from '../time-picker.vue';
import Textarea from '../ui/textarea/Textarea.vue';
import PlatformSelector from './PlatformSelector.vue';
import PublishingOptions from './PublishingOptions.vue';
import TagsInput from '../ui/tags-input/TagsInput.vue';
import TagsInputItem from '../ui/tags-input/TagsInputItem.vue';
import TagsInputItemText from '../ui/tags-input/TagsInputItemText.vue';
import TagsInputItemDelete from '../ui/tags-input/TagsInputItemDelete.vue';
import TagsInputInput from '../ui/tags-input/TagsInputInput.vue';
import DialogClose from '../ui/dialog/DialogClose.vue';
import vueFilePond from 'vue-filepond';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.esm.js';
import 'filepond/dist/filepond.min.css';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';
import { toast } from 'vue-sonner';
const FilePond = vueFilePond(FilePondPluginImagePreview);
const filepond = ref();
const myFiles = ref(null);
const startsAt = ref(new Date());
const page = usePage();
const propsData = defineProps({
    isOpen: Boolean,
    platforms: Object,
});
const form = useForm({
    title: '',
    content: '',
    scheduledDate: null,
    image: null,
    platforms: [],
    tags: [],
    publishOption: '',
});

//  filepond init function
const handleFilePondInit = () => {
    // example of instance method call on pond reference
    filepond.value.getFiles();
};
// Handling multi Image load/store Filepone
const handleFilePondLoad = (response) => {
    form.image = response;
    return response;
}

// Handling multi Image Revert Filepone
const handleFilePondRevert = (attachmentID, load, error) => {
    form.image = null;
    router.post(route('revert'), { attachment_path: attachmentID }, {
        preserveScroll: true,
    });
}

const isDialogOpened = computed(() => {
    return propsData.isOpen;
})
const bindCalendarDate = (date) => {
    const formatedDate = new Date(date);


    formatedDate.setHours(startsAt.value.getHours());
    formatedDate.setMinutes(startsAt.value.getMinutes());
    formatedDate.setSeconds(startsAt.value.getSeconds());

    form.scheduledDate = formatedDate.toLocaleString('sv-SE').replace('T', ' ');

}

const bindSelectedPlatforms = (platformArray) => {
    form.platforms = platformArray;
}


const bindSelectedPublishingOption = (value) => {
    form.publishOption = value;
}


// Dynamic submit button text based on selected option
const submitButtonText = computed(() => {
    switch (form.publishOption) {
        case 'publish':
            return 'Publish Now';
        case 'schedule':
            return 'Schedule Post';
        case 'draft':
            return 'Save as Draft';
        default:
            return 'Submit';
    }
});

watch(startsAt, (value) => {
    if (form.scheduledDate) {
        bindCalendarDate(form.scheduledDate);
    }
}, { deep: true });

const submit = () => {
    form.post(route('posts.store'), {
        onSuccess: () => {
            toast.success('Post has been created.')
            form.reset();
        },
        onError: () => {
            toast.error('Something went wrong while trying to create the post. Please try again later')
        }
    })
}

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
