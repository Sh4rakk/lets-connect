export default ({ imageUrl }) => ({
    preview: imageUrl,
    fileName: imageUrl ? decodeURIComponent(imageUrl.split('/').pop()) : '',
    dragging: false,
    imageError: null,

    pick(event) {
        this.useFile(event.target.files[0]);
    },

    drop(event) {
        this.dragging = false;

        const file = event.dataTransfer.files[0];
        if (!file) {
            return;
        }

        const transfer = new DataTransfer();
        transfer.items.add(file);
        this.$refs.image.files = transfer.files;

        this.useFile(file);
    },

    useFile(file) {
        this.imageError = null;

        if (!file) {
            return;
        }

        if (!['image/jpeg', 'image/png'].includes(file.type)) {
            this.reject('De afbeelding moet een JPG of PNG zijn.');
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            this.reject('De afbeelding mag maximaal 2 MB zijn.');
            return;
        }

        const reader = new FileReader();
        reader.onload = () => {
            this.preview = reader.result;
            this.fileName = file.name;
        };
        reader.readAsDataURL(file);
    },

    reject(message) {
        this.$refs.image.value = '';
        this.preview = imageUrl;
        this.fileName = imageUrl ? decodeURIComponent(imageUrl.split('/').pop()) : '';
        this.imageError = message;
    },
});
