(function() {
	var $, $$, $slotitems, MaterialIcon, ROOT, React, ReactBootstrap, SlotitemIcon, _, layout, path, useSVGIcon;

	ROOT = window.ROOT, layout = window.layout, _ = window._, $ = window.$, $$ = window.$$, React = window.React, ReactBootstrap = window.ReactBootstrap, useSVGIcon = window.useSVGIcon;

	$slotitems = window.$slotitems;

	SlotitemIcon = React.createClass({displayName: "SlotitemIcon",
		name: 'SlotitemIcon',
		render: function() {
			return React.createElement("img", {
				"src": "assets/img/slotitem/" + (this.props.slotitemId + 100) + ".png",
				"className": this.props.className + " png"
			});
		}
	});

	MaterialIcon = React.createClass({displayName: "MaterialIcon",
		name: 'MaterialIcon',
		render: function() {
			return React.createElement("img", {
				"src": "assets/img/material/0" + this.props.materialId + ".png",
				"className": this.props.className + " png"
			});
		}
	});

	return {
		SlotitemIcon: SlotitemIcon,
		MaterialIcon: MaterialIcon
	};

})();
