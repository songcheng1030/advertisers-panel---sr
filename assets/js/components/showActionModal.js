const ACTION_MODAL_SHOW_TIME = 4000;
/**
 * Show a modal with a message
 * @param modal type of modal (success, error, warning)
 * @param message translation key
 */
function showActionModal(modal, message = null) {
  let $actionModal = $(".action-modals." + modal);
  $actionModal.addClass("active");

  if (message) {
    $actionModal
      .find(".js-action-modal-message")
      .text(Translator.trans(message));
  } else {
    $actionModal
      .find(".js-action-modal-message")
      .text(Translator.trans("action_modal." + modal));
  }

  setTimeout(function() {
    $(".action-modals").removeClass("active");
  }, ACTION_MODAL_SHOW_TIME);
}

module.exports = showActionModal;
