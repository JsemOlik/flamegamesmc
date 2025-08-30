<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger
} from '@/components/ui/accordion';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    name: '',
    code: '',
    remember: false
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('code')
    });
};
</script>

<template>
    <div class="relative min-h-screen">
        <!-- Background layer (same look/feel as Staff page) -->
        <div class="absolute inset-0 z-0 bg-black" :style="{
            backgroundImage: `url('/header.png')`,
            backgroundSize: 'cover',
            backgroundPosition: 'top center',
            backgroundRepeat: 'no-repeat',
            opacity: 0.1
        }" aria-hidden="true"></div>

        <!-- Foreground content -->
        <div class="relative z-10">
            <AuthBase title="Přihlaš se do svého účtu" description="Zadej svůj Minecraft username, a svůj login kód">

                <Head title="Přihlášení" />

                <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="flex flex-col gap-6">
                    <div class="grid gap-6">
                        <div class="grid gap-2">
                            <Label for="name">MC Uživatelské Jméno</Label>
                            <Input id="name" type="text" required autofocus :tabindex="1" autocomplete="username"
                                v-model="form.name" placeholder="JsemOlik" />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <div class="flex items-center justify-between">
                                <Label for="code">Login kód</Label>
                            </div>
                            <Input id="code" type="text" required :tabindex="2" autocomplete="one-time-code"
                                v-model="form.code" placeholder="xxxxxxxx" />
                            <InputError :message="form.errors.code" />
                        </div>

                        <div class="flex items-center justify-between">
                            <Label for="remember" class="flex items-center space-x-3">
                                <Checkbox id="remember" v-model="form.remember" :tabindex="3" />
                                <span>Zapamatovat si mě</span>
                            </Label>
                        </div>

                        <Button type="submit" class="mt-4 w-full" :tabindex="4" :disabled="form.processing">
                            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                            Přihlásit se
                        </Button>
                    </div>

                    <Accordion type="single" collapsible class="text-muted-foreground">
                        <AccordionItem value="item-1">
                            <AccordionTrigger>Nevíš jak získat svůj login kód?</AccordionTrigger>
                            <AccordionContent>
                                Joini se na
                                <code
                                    class="px-1 py-0.5 rounded bg-neutral-900 text-neutral-100 font-mono">mc.qpvp.pro</code>,
                                a napiš do chatu
                                <code
                                    class="px-1 py-0.5 rounded bg-neutral-900 text-neutral-100 font-mono">/fglogin generate</code>
                                a ukáže se ti tvůj kód
                            </AccordionContent>
                        </AccordionItem>
                    </Accordion>
                </form>
            </AuthBase>
        </div>
    </div>
</template>