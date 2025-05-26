<template>

    <!-- LinkedIn Mockup -->
    <section class="mb-12">
        <h2 class="text-2xl font-semibold mb-4 flex items-center gap-2">
            <Badge class="bg-blue-600 text-white">LinkedIn</Badge>
            Mockup
        </h2>

        <Card class="overflow-hidden border-gray-200">
            <CardHeader class="flex flex-row items-start gap-3 pb-2">
                <Avatar class="w-12 h-12">
                    <AvatarImage src="https://i.pravatar.cc/150?img=2" />
                    <AvatarFallback>JD</AvatarFallback>
                </Avatar>
                <div>
                    <CardTitle class="text-base">{{ post.user.name }}</CardTitle>
                    <div class="flex items-center text-xs text-muted-foreground mt-1">
                        <Clock class="h-3 w-3 mr-1" />
                        <span>{{ post.publishedAt }} • </span>
                        <Globe class="h-3 w-3 mx-1" />
                    </div>
                </div>
                <Button variant="ghost" size="icon" class="ml-auto">
                    <MoreHorizontal class="h-5 w-5" />
                </Button>
            </CardHeader>

            <CardContent class="px-4 py-2">
                <p class="text-sm mb-3">
                    {{ post.content }}
                </p>
                <div class="space-x-1">
                    <span v-for="(tag, index) in post.tags" :key="index" class="text-sm text-sky-500 mt-1">
                        #{{ tag }}
                    </span>
                </div>
                <img v-if="post.image" :src="post?.image" alt="Team working" class="w-full h-auto rounded-md" />
            </CardContent>

            <div class="px-4 py-2">
                <div class="flex items-center text-xs text-muted-foreground">
                    <ThumbsUp class="h-3 w-3 mr-1 text-blue-600 fill-blue-600" />
                    <span>348 • 57 comments • 23 shares</span>
                </div>
                <Separator class="my-2" />
                <div class="flex justify-between">
                    <Button variant="ghost" size="sm" class="flex items-center gap-1 text-sm">
                        <ThumbsUp class="h-5 w-5" />
                        <span>Like</span>
                    </Button>
                    <Button variant="ghost" size="sm" class="flex items-center gap-1 text-sm">
                        <MessageSquare class="h-5 w-5" />
                        <span>Comment</span>
                    </Button>
                    <Button variant="ghost" size="sm" class="flex items-center gap-1 text-sm">
                        <Repeat class="h-5 w-5" />
                        <span>Share</span>
                    </Button>
                    <Button variant="ghost" size="sm" class="flex items-center gap-1 text-sm">
                        <Send class="h-5 w-5" />
                        <span>Send</span>
                    </Button>
                </div>
            </div>
        </Card>

    </section>

</template>

<script setup>
import {
    Send,
    ThumbsUp,
    MessageSquare,
    Repeat,
    MoreHorizontal,
    Clock,
    Globe,
    CheckCircle,
    Briefcase,
} from 'lucide-vue-next'

import { Card } from '@/components/ui/card'
import { CardHeader } from '@/components/ui/card'
import { CardTitle } from '@/components/ui/card'
import { CardDescription } from '@/components/ui/card'
import { CardContent } from '@/components/ui/card'
import { Avatar } from '@/components/ui/avatar'
import { AvatarImage } from '@/components/ui/avatar'
import { AvatarFallback } from '@/components/ui/avatar'
import { Badge } from '@/components/ui/badge'
import { Separator } from '@/components/ui/separator'
import Button from '@/components/ui/button/Button.vue'
const propsData = defineProps({
    post: Object,
});
</script>