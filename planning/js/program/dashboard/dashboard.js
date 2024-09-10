$(".staffAvailableCard").hide();
$(".machinesAvailableCard").hide();

$(".staffAvailableCard").click(function (e) {
  e.preventDefault();
  $(".staffAvailableCardChart").show();
});

$(".machinesAvailableCard").click(function (e) {
  e.preventDefault();
  $(".machinesAvailableCardChart").show();
});
