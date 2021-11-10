// const $ = require('jquery');
// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require('bootstrap');

// or you can include specific pieces
// require('bootstrap/js/dist/tooltip');
// require('bootstrap/js/dist/popover');

$(document).ready(function() {
    $('[data-toggle="popover"]').popover();


    /**************************************
     * CANNOT UNCHECK
     *************************************/

    $('#gift_form_giftGroup input').each((index, currentElement) => {
        if(currentElement.checked){
            currentElement.disabled = true;
        }
    })

    $('table').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json'
        }
    });
});

/*****************************************
 * COPY ON CLICK
 ***************************************/
window.copyToClipboard= function copyToClipboard(element) {
    console.log("copy");
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).text()).select();
    document.execCommand("copy");
    $temp.remove();
}


/**************************************
 * TOGGLE MODAL
 *************************************/

var deleteModal = document.getElementById('deleteModal')
if(deleteModal){
    deleteModal.addEventListener('show.bs.modal', function (event) {
        // Button that triggered the modal
        var button = event.relatedTarget
        // Extract info from data-bs-* attributes
        var title = button.getAttribute('data-bs-title')
        var link = button.getAttribute('data-bs-url')
        var buttontext = button.getAttribute('data-bs-button')
        var body = button.getAttribute('data-bs-body')

        // If necessary, you could initiate an AJAX request here
        // and then do the updating in a callback.
        //
        // Update the modal's content.
        var modalTitle = deleteModal.querySelector('.modal-title')
        modalTitle.innerHTML = title;

        var modalLink = deleteModal.querySelector('.modal-link')
        modalLink.href= link
        modalLink.innerHTML= buttontext

        var modalBody = deleteModal.querySelector('.modal-body')
        modalBody.innerHTML = body
    })

}

var formModal = document.getElementById('formModal')
if(formModal){
    formModal.addEventListener('show.bs.modal', function (event) {
        // Button that triggered the modal
        var button = event.relatedTarget
        // Extract info from data-bs-* attributes
        var id = button.getAttribute('data-bs-id')


        // If necessary, you could initiate an AJAX request here
        // and then do the updating in a callback.
        //
        // Update the modal's content.
        var modalId = formModal.querySelector('#pot_form_id')

        modalId.value = id;
    })

}


