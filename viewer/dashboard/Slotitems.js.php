(function() {
	var $, $$, OverlayTrigger, ROOT, React, ReactBootstrap, SlotitemIcon, Slotitems, Tooltip, _, getBackgroundStyle, path;

	$ = window.$, $$ = window.$$, _ = window._, React = window.React, ReactBootstrap = window.ReactBootstrap, ROOT = window.ROOT;

	OverlayTrigger = ReactBootstrap.OverlayTrigger, Tooltip = ReactBootstrap.Tooltip;

	SlotitemIcon = CommonIcon.SlotitemIcon;

	getBackgroundStyle = function() {
		return {
			backgroundColor: 'rgba(33, 33, 33, 0.7)'
		};
	};

	Slotitems = React.createClass({displayName: "Slotitems",
		render: function() {
			var $slotitems, _slotitems, i, item, itemId;
			return React.createElement("div", {
				"className": "slotitems"
			}, ((function() {
				var j, len, ref, ref1, results;
				$slotitems = window.$slotitems, _slotitems = window._slotitems;
				ref = this.props.data;
				if (!ref) {ref=[]};
				results = [];
				for (i = j = 0, len = ref.length; j < len; i = ++j) {
					itemId = ref[i];
					if (!(itemId !== -1 && (_slotitems[itemId] != null))) {
						continue;
					}
					item = _slotitems[itemId];
					results.push(React.createElement("div", {
						"key": i,
						"className": "slotitem-container"
					}, React.createElement(OverlayTrigger, {
						"placement": 'left',
						"overlay": React.createElement(Tooltip, {
							"id": "fleet-" + this.props.fleet + "-slot-" + this.props.key + "-item-" + i + "-level"
						}, item.api_name, (item.api_level > 0 ? React.createElement("strong", {
							"style": {
								color: '#45A9A5'
							}
						}, " ★", item.api_level) : ''), ((item.api_alv != null) && (1 <= (ref1 = item.api_alv) && ref1 <= 7) ? React.createElement("img", {
							"className": 'alv-img',
							"src": 'assets/img/airplane/alv' + item.api_alv + ".png"
						}) : ''))
					}, React.createElement("span", null, React.createElement(SlotitemIcon, {
						"key": itemId,
						"className": 'slotitem-img',
						"slotitemId": item.api_type[3]
					}), React.createElement("span", {
						"className": "slotitem-onslot " + ((item.api_type[3] >= 6 && item.api_type[3] <= 10) || (item.api_type[3] >= 21 && item.api_type[3] <= 22) || item.api_type[3] === 33 || i === 5 ? 'show' : 'hide') + " " + (this.props.onslot[i] < this.props.maxeq[i] && i !== 5 ? 'text-warning' : ''),
						"style": getBackgroundStyle()
					}, (i === 5 ? '+' : this.props.onslot[i]))))));
				}
				return results;
			}).call(this)));
		}
	});

	return Slotitems;

})();
