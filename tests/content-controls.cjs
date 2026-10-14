const fs = require('node:fs');
const vm = require('node:vm');
const assert = require('node:assert/strict');
class Element {
  constructor(tag) { this.tag = tag; this.children = []; this.style = {}; this.dataset = {}; this.listeners = {}; }
  append(...children) { this.children.push(...children); }
  replaceChildren(...children) { this.children = children; }
  addEventListener(type, fn) { this.listeners[type] = fn; }
  removeAttribute() {}
  querySelector(selector) { return this.children.find(e => (e.className || '').split(' ').includes(selector.slice(1))) || this.children.map(e => e.querySelector(selector)).find(Boolean); }
}
const wp = { customize: { controlConstructor: {}, Control: { extend: spec => spec } }, media: { attachment: () => ({ fetch: () => Promise.resolve(), get: () => 'logo.png' }) } };
vm.runInNewContext(fs.readFileSync('assets/js/content-controls.js', 'utf8'), { wp, jQuery: {}, document: { createElement: tag => new Element(tag) } });
function open(value) {
  const root = new Element('div'); root.className = 'rbc68-list-control'; root.dataset.fields = JSON.stringify({name:'Nom',url:'Lien',logo:'Image'});
  const rows = new Element('div'); rows.className = 'rbc68-list-rows';
  const add = new Element('button'); add.className = 'rbc68-list-add'; root.append(rows,add);
  const container = new Element('div'); container.append(root);
  let callback;
  const setting = { get: () => value, set: next => { value = next; if(callback) callback(next); }, bind: fn => { callback = fn; } };
  wp.customize.controlConstructor['rbc68-list'].ready.call({ container:[container], setting });
  return {rows,add,setting};
}
let ui = open([{name:'Un',logo:0},{name:'Deux',logo:0},{name:'Trois',logo:0}]);
assert.equal(ui.rows.children.length,3,'les trois sponsors enregistrés réapparaissent');
const name = ui.rows.children[0].children[0].children[0];
name.value='Modifié'; name.listeners.input(); assert.equal(ui.setting.get()[0].name,'Modifié');
ui.rows.children[1].children.at(-1).onclick(); assert.equal(ui.setting.get().length,2);
ui = open(ui.setting.get()); assert.equal(ui.rows.children.length,2,'suppression persistée après réouverture');
while(ui.rows.children.length) ui.rows.children[0].children.at(-1).onclick();
assert.equal(ui.setting.get().length,0); assert.equal(open(ui.setting.get()).rows.children.length,0);
ui.add.onclick(); assert.equal(ui.setting.get().length,1,'ajout enregistré');
assert.equal(open('[{"name":"Ancien format"}]').rows.children.length,1,'compatibilité JSON');
console.log('OK — chargement, modification, suppression, suppression totale, réouverture et ajout');
