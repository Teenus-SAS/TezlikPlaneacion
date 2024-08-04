!(function (r) {
  "use strict";
  if (
    (r("#event-category").length && r("#event-category").select2(),
    window.FullCalendar)
  ) {
    r("#external-events .fc-event").each(function () {
      var e = {
        title: r.trim(r(this).text()),
        className: r.trim(r(this).attr("class")),
      };
      r(this).data("eventObject", e),
        r(this).draggable({ zIndex: 999, revert: !0, revertDuration: 0 });
    });
    var e = new Date(),
      t = e.getDate(),
      n = e.getMonth(),
      l = e.getFullYear();
    r("#fc_calendar").fullCalendar({
      header: {
        left: "prev,next today",
        center: "title",
        right: "month,basicWeek,basicDay",
      },
      editable: !0,
      eventLimit: !0,
      droppable: !0,
      drop: function (e, t) {
        var a = r(this).data("eventObject"),
          n = r.extend({}, a);
        (n.start = e),
          (n.allDay = t),
          r("#fc_calendar").fullCalendar("renderEvent", n, !0),
          r("#drop-remove").is(":checked") && r(this).remove();
      },
      events: [
       /*  {
          title: "All Day Event",
          start: new Date(l, n, 1),
          className: "bg-info",
        },
        {
          title: "Long Event",
          start: new Date(l, n, t - 5),
          end: new Date(l, n, t - 2),
        },
        {
          id: 999,
          title: "Repeating Event",
          start: new Date(l, n, t - 3, 16, 0),
          allDay: !1,
          className: "bg-danger",
        },
        {
          id: 999,
          title: "Repeating Event",
          start: new Date(l, n, t + 4, 16, 0),
          allDay: !1,
        },
        {
          title: "Meeting",
          start: new Date(l, n, t, 10, 30),
          allDay: !1,
          className: "bg-warning",
        },
        {
          title: "Lunch",
          start: new Date(l, n, t, 12, 0),
          end: new Date(l, n, t, 14, 0),
          allDay: !1,
        },
        {
          title: "Birthday Party",
          start: new Date(l, n, t + 1, 19, 0),
          end: new Date(l, n, t + 1, 22, 30),
          allDay: !1,
        },
        {
          title: "Click for Google",
          start: new Date(l, n, 28),
          end: new Date(l, n, 29),
          url: "http://google.com/",
        }, */
      ],
    }),
      r("#add_event_form").on("submit", function (e) {
        e.preventDefault();
        var t = r(this).find(".form-control"),
          a = t.val();
        if (3 <= a.length) {
          var n = "new" + Math.random().toString(36).substring(7);
          r("#external-events").append(
            '<div id="' + n + '" class="fc-event">' + a + "</div>"
          );
          var l = { title: r.trim(r("#" + n).text()) };
          r("#" + n).data("eventObject", l),
            r("#" + n).draggable({
              revert: !0,
              revertDuration: 0,
              zIndex: 999,
            }),
            t.val("").focus();
        } else t.focus();
      });
    r("#fc_calendar").fullCalendar("renderEvent", {}, !0);
  } else alert("Calendar plugin is not installed");
  r("#createEventForm").on("submit", function (e) {
    e.preventDefault();
    var t = !0,
      a = { start: new Date(l, n, 28), end: new Date(l, n, 29) };
    if (
      (r(this)
        .find(".form-control")
        .each(function () {
          if ("" === r(this).val())
            return r(this).addClass("is-invalid"), void (t = !1);
          var e = r(this).attr("data-key");
          (a[e] = r(this).val()), r(this).removeClass("is-invalid");
        }),
      t)
    )
      return (
        r("#fc_calendar").fullCalendar("renderEvent", a, !0),
        r(this).find(".floating-label").removeClass("enable-floating-label"),
        r(this)[0].reset(),
        r("#addeventmodal").modal("toggle"),
        t
      );
  });
})(jQuery);
