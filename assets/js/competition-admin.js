document.addEventListener('DOMContentLoaded', function () {
  const team = document.getElementById('rbc-comp-team');
  const opponent = document.getElementById('rbc-comp-opponent');
  if (!team || !opponent) return;
  team.addEventListener('change', function () {
    opponent.replaceChildren(new Option('Choisir un adversaire', ''));
    const names = JSON.parse(team.options[team.selectedIndex].dataset.opponents || '[]');
    names.forEach(function (name) { if (name) opponent.add(new Option(name, name)); });
  });
});
