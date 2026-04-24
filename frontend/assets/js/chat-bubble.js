jQuery(document).ready(function ($) {
    let chatButton = $('#myaio-chat-button');
    let chatPopup = $('#myaio-chat-popup');
    let chatClose = $('#myaio-chat-close');
    let chatForm = $('#myaio-chat-form');
    let chatResponse = $('#myaio-chat-response');

    // Toggle popup
    chatButton.on('click', function () {
        chatPopup.toggleClass('myaio-hidden');
    });

    chatClose.on('click', function () {
        chatPopup.addClass('myaio-hidden');
    });

    // Handle form submission
    chatForm.on('submit', function (e) {
        e.preventDefault();

        let submitBtn = chatForm.find('button[type="submit"]');
        submitBtn.prop('disabled', true).text('Đang gửi...');
        chatResponse.hide().removeClass('myaio-success-msg myaio-error-msg').text('');

        let formData = {
            action: 'myaio_submit_chat',
            nonce: myaioChat.nonce,
            name: chatForm.find('input[name="myaio_chat_name"]').val(),
            email: chatForm.find('input[name="myaio_chat_email"]').val(),
            message: chatForm.find('textarea[name="myaio_chat_message"]').val()
        };

        $.post(myaioChat.ajax_url, formData, function (response) {
            submitBtn.prop('disabled', false).text('Gửi tin nhắn');

            if (response.success) {
                chatResponse.addClass('myaio-success-msg').text(response.data.message).fadeIn();
                chatForm[0].reset();
                setTimeout(function () {
                    chatPopup.addClass('myaio-hidden');
                    chatResponse.hide();
                }, 3000);
            } else {
                chatResponse.addClass('myaio-error-msg').text(response.data.message || 'Lỗi kết nối.').fadeIn();
            }
        }).fail(function () {
            submitBtn.prop('disabled', false).text('Gửi tin nhắn');
            chatResponse.addClass('myaio-error-msg').text('Có lỗi xảy ra, vui lòng thử lại.').fadeIn();
        });
    });
});
