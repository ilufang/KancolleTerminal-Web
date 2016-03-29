(function() {
	var ItemInfoArea, ItemInfoCheckboxArea, ItemInfoTableArea, React;

	React = window.React;

	ItemInfoTableArea = <?php include 'item-info-table-area.js.php';?>

	ItemInfoCheckboxArea = <?php include 'item-info-checkbox-area.js.php';?>

	ItemInfoArea = React.createClass({displayName: "ItemInfoArea",
		getInitialState: function() {
			return {
				itemTypeBoxes: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33]
			};
		},
		filterRules: function(boxes) {
			return this.setState({
				itemTypeBoxes: boxes
			});
		},
		render: function() {
			return React.createElement("div", null, React.createElement(ItemInfoCheckboxArea, {
				"filterRules": this.filterRules
			}), React.createElement(ItemInfoTableArea, {
				"itemTypeBoxes": this.state.itemTypeBoxes
			}));
		}
	});

	return ItemInfoArea;

})();
