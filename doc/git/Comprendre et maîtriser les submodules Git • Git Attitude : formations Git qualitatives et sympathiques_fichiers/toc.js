(function() {
  function initTOC() {
    $('#toc').remove();
    var article = $('article[role=article]');
    if (0 == article.length) return;
    var header = $('header', article), titles = $('h2, h3', article);
    if (0 == header.length || titles.length < 3) return;
    var markup = ['<ul id="toc">'];
    $(titles).each(function(heading) {
      heading = $(heading);
      var text = heading.html().replace(/<(span|sup|a)\b.*?<\/\1>/gi, '');
      var id = heading.attr('id');
      if (!id) {
        id = heading.text().toLocaleLowerCase().replace(/\W+/, '-');
        heading.attr('id', id);
      }
      var klass = 'H2' == heading[0].tagName ? '' : ' class="level3"';
      markup.push('<li' + klass + '><a href="#' + id + '">' + text + '</a></li>');
    });
    markup.push('</ul>');
    header.after(markup.join("\n"));
  }

  $.domReady(initTOC);
  $(document).on('headings:added', initTOC);
})();
