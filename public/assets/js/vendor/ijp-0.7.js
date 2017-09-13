var Ical = function Ical(){
	this.version = '';
	this.prodid = '';
	this.events = [];
	this.todos = [];
	this.journals = [];
	this.freebusys = [];
}
var xprops = 'x-[^:;]+';
var ianaprops = '[\\w]+[^:;]+'
var icalParser = {
	icals : [],
	propsList : {
		'event':'(dtstamp|uid|dtstart|class|created|description|geo|last-mod|location|organizer|priority|seq|status|summary|transp|url|recurid|rrule|dtend|duration|attach|attendee|categories|comment|contact|exdate|rstatus|related|resources|rdate|'+xprops+'|'+ianaprops+')',
		'freebusy':'(dtstamp|uid|contact|dtstart|dtend|organizer|url|attendee|comment|freebusy|rstatus|'+xprops+'|'+ianaprops+')',
		'journal':'(dtstamp|uid|class|created|dtstart|last-mod|organizer|recurid|seq|status|summary|url|rrule|attach|attendee|categories|comment|contact|description|exdate|related|rdate|rstatus|'+xprops+'|'+ianaprops+')',
		'todo':'(dtstamp|uid|class|completed|created|description|dtstart|geo|last-mod|location|organizer|percent|priority|recurid|seq|status|summary|url|rrule|due|duration|attach|attendee|categories|comment|contact|exdate|rstatus|related|resources|rdate|'+xprops+'|'+ianaprops+')'
	},
	parseIcal : function(icsString){
		var cals = icsString.match(/BEGIN:VCALENDAR\r?\n(.*\r?\n)+?END:VCALENDAR/ig);
		for(var index in cals){
			//console.log("--->"+index+" "+cals[index]);
			var ical = new Ical(); 
			ical.version = this.getValue('VERSION',cals[index]);
			ical.prodid = this.getValue('PRODID',cals[index]);
			cals[index] = cals[index].replace(/\r\n /g,'');
			cals[index] = cals[index].replace(/BEGIN:VCALENDAR\r?\n/ig,'');
			var reg = /BEGIN:(V.*?)\r?\n(.*\r?\n)+?END:\1/gi;
			matches = cals[index].match(reg);
			if(matches){
				for(i=0;i<matches.length;i++){
					//console.log('---------->'+matches[i]+"\n<------------");
					this.parseVComponent(matches[i],ical);
				}
			}
			this.icals[this.icals.length] = ical;
		}
	},
	parseVComponent : function(vComponent,ical){
		var nameComponent = vComponent.match(/BEGIN:V([^\s]+)/i)[1].toLowerCase();
		vComponent = vComponent.replace(/\r?\n[\s]+/igm,''); //unfolding
		vComponent = vComponent.replace(/(^begin|^end):.*/igm,'');
		//console.log(nameComponent+' ++++ '+vComponent);
		var props = vComponent.match(new RegExp(this.propsList[nameComponent]+'[:;].*','gim'));
		if(props){
			var component=[];
			for(var index in props){
				var nom = props[index].replace(/[:;].*$/,'');
				//console.log("--vcompo "+index+" "+nom);
				var propKey = /*'prop_'+*/nom.toLowerCase();
				if(component[propKey]===undefined) component[propKey] = [];
				component[propKey][component[propKey].length] = this.getValue(nom,props[index]);
				component['raw'] = vComponent;
			}
			if(ical[nameComponent+'s'] !== undefined)
				ical[nameComponent+'s'][ical[nameComponent+'s'].length] = component;
		}
	},
	getValue: function(propName,line){
		//console.log(line);
		var prop={};
		line = line.replace(/^\s+/g,'').replace(/\s+$/gi,'');
		reg = new RegExp('('+propName+')((?:;[^=]*=[^;:\n]*)*):([^\n\r]*)','gi');
		var matches = reg.exec(line);
		if(matches){ //on a trouvÃ© la propriÃ©tÃ© cherchÃ©e
			//console.log(propName+' ==] params='+RegExp.$2+' / valeur='+RegExp.$3);
			var valeur = RegExp.$3;
			var tab_params=[];
			if(RegExp.$2.length>0){ //il y a des paramÃ¨tres associÃ©s
				var params = RegExp.$2.substr(1).split(';');
				var pair;
				for(k=0;k<params.length;k++){
					pair = params[k].split('=');
					if(!pair[1]) pair[1] = pair[0];
					tab_params[pair[0]] = pair[1];
				}
			}
			prop = { value:valeur,name:propName };
			if(Object.keys(tab_params).length>0)
				prop.params = tab_params;
		}
		return prop;
	},
};