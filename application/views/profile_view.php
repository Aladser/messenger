<div class='container font-roboto mt-4'>
    <div class='d-flex justify-content-center'>
        <div class='pe-4'>
            <img src="application/images/ava_profile.png" id='profile-img' class="profile-img rounded-circle" alt="Avatar" />
        </div>
        <div class='p-4'>
            <table class='table'>
                <tr>
                    <td>email:</td>
                    <td>aladser@mail.ru</td>
                </tr>
                <tr>
                    <td>nickname:</td>
                    <td>Aladser</td>
                </tr>
            </table>
            <div class="form-check form-switch mb-3 d-flex justify-content-center">
                <input class="form-check-input" type="checkbox" id="hide-email-input">
                <label class="form-check-label" for="hide-email-input">&nbsp; скрыть почту</label>
            </div>
            <button class='btn btn-bg-C4C4C4 text-white w-100 hidden' id='save-profile-settings-btn'>Сохранить</button>
        </div>
        <div class='p-4 w-25'>
            <form method='post' id='upload-file-form' enctype="multipart/form-data">
                <input type="file" id="select-file-input" name='image' class='hidden'>
                <input type="submit" id='upload-file-btn' class='hidden'>
                <button class='btn btn-bg-C4C4C4 text-white w-100 mb-2' id='edit-photo-btn'>Изменить фото</button>
            </form>
            
            <button class='btn btn-bg-C4C4C4 text-white w-100 mb-2'>Установить nickname</button>
            <button class='btn btn-bg-C4C4C4 text-white w-100 mb-2'>Изменить пароль</button>  <!-- на будущее -->
        </div>
    </div>
</div>