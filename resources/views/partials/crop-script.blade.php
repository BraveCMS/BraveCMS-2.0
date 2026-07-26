<script>
    document.addEventListener('DOMContentLoaded', () => {
        const avatarInput = document.getElementById('avatar'),
            cropModal = document.getElementById('cropModal'),
            cropImage = document.getElementById('crop-image'),
            avatarPreview = document.querySelector('.avatar-preview'),
            croppedAvatar = document.getElementById('cropped_avatar'),
            cropButton = document.getElementById('crop-button'),
            cropClose = document.getElementById('crop-close'),
            cropCancel = document.getElementById('crop-cancel');

        let cropper;

        const closeModal = () => {
            cropper?.destroy();
            cropper = null;
            cropModal.classList.remove('show');
            cropModal.style.display = 'none';
            document.body.classList.remove('modal-open');
            document.getElementById('crop-backdrop')?.remove();
        };

        avatarInput.onchange = e => {
            const file = e.target.files[0];
            if (!file) return;

            cropImage.src = URL.createObjectURL(file);
            cropModal.style.display = 'block';
            cropModal.classList.add('show');
            document.body.classList.add('modal-open');

            if (!document.getElementById('crop-backdrop'))
                document.body.insertAdjacentHTML('beforeend',
                    '<div id="crop-backdrop" class="modal-backdrop fade show"></div>');

            cropImage.onload = () => setTimeout(() => {
                cropper?.destroy();
                cropper = new Cropper(cropImage, {
                    aspectRatio: 1,
                    viewMode: 2,
                    autoCropArea: 1
                });
            }, 100);
        };

        cropButton.onclick = () => {
            if (!cropper) return;
            const data = cropper.getCroppedCanvas({
                width: 400,
                height: 400
            }).toDataURL('image/png');
            croppedAvatar.value = data;
            avatarPreview.src = data;
            closeModal();
        };

        cropClose.onclick = closeModal;
        cropCancel.onclick = closeModal;
    });
</script>
