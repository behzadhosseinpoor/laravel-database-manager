<template>
    <button
        @click="download"
        class="px-3 py-1.5 rounded-md font-medium
           flex items-center gap-1 transition cursor-pointer
           shadow-sm hover:shadow-md justify-center
           text-white bg-blue-600 hover:bg-blue-700
           dark:bg-blue-700 dark:hover:bg-blue-800">
        <i class="fa-solid fa-download"></i>
        Download
    </button>
</template>

<script>
export default {
    props: {
        base64: String,
        fileName: {type: String, default: "download.bin"},
        mimeType: {type: String, default: "application/octet-stream"}
    },

    methods: {
        download() {
            const byte = atob(this.base64);
            const len = byte.length;
            const buffer = new Uint8Array(len);

            for (let i = 0; i < len; i++) {
                buffer[i] = byte.charCodeAt(i);
            }

            const blob = new Blob([buffer], {type: this.mimeType});
            const url = URL.createObjectURL(blob);

            const a = document.createElement("a");
            a.href = url;
            a.download = this.fileName;
            a.click();

            URL.revokeObjectURL(url);
        }
    }
}
</script>
