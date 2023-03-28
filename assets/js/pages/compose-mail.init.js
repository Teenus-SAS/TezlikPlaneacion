let editor;

function composeMail() {
  $('#compose-editor').length &&
    ClassicEditor.create(document.querySelector('#compose-editor'))
      .then(function (o) {
        editor = o;
      })
      .catch(function (o) {
        console.error('error', o);
      });
}
$(function () {
  composeMail();
});

/* Obtener contenido */
getContent = () => {
  content = editor.getData();

  if (content.includes('<p>'))
    content = content.replace('<p>', '').replace('</p>', '');

  return content;
};

/* Estabecer contenido */
setContent = (data) => {
  editor.setData(data);
};
