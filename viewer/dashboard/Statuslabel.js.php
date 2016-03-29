(function() {
	var $, $$, FontAwesome, Label, OverlayTrigger, React, ReactBootstrap, StatusLabel, Tooltip, _, ref;

	_ = window._, $ = window.$, $$ = window.$$, React = window.React, ReactBootstrap = window.ReactBootstrap, FontAwesome = window.FontAwesome;

	OverlayTrigger = ReactBootstrap.OverlayTrigger, Tooltip = ReactBootstrap.Tooltip, Label = ReactBootstrap.Label;

	StatusLabel = React.createClass({displayName: "StatusLabel",
		shouldComponentUpdate: function(nextProps, nextState) {
			return !_.isEqual(nextProps.label, this.props.label);
		},
		render: function() {
			if ((this.props.label != null) && this.props.label === 0) {
				return React.createElement(OverlayTrigger, {
					"placement": "top",
					"overlay": React.createElement(Tooltip, {
						"id": "statuslabel-status-0"
					}, __('Retreated'))
				}, React.createElement(Label, {
					"bsStyle": "danger"
				}, React.createElement(FontAwesome, {
					"key": 0.,
					"name": 'exclamation-circle'
				})));
			} else if ((this.props.label != null) && this.props.label === 1) {
				return React.createElement(OverlayTrigger, {
					"placement": "top",
					"overlay": React.createElement(Tooltip, {
						"id": "statuslabel-status-1"
					}, __('Repairing'))
				}, React.createElement(Label, {
					"bsStyle": "info"
				}, React.createElement(FontAwesome, {
					"key": 0.,
					"name": 'wrench'
				})));
			} else if ((this.props.label != null) && this.props.label === 2) {
				return React.createElement(OverlayTrigger, {
					"placement": "top",
					"overlay": React.createElement(Tooltip, {
						"id": "statuslabel-status-2"
					}, __('Ship tag: %s', 'E1, E2, E3'))
				}, React.createElement(Label, {
					"bsStyle": "info"
				}, React.createElement(FontAwesome, {
					"key": 0.,
					"name": 'tag'
				})));
			} else if ((this.props.label != null) && this.props.label === 3) {
				return React.createElement(OverlayTrigger, {
					"placement": "top",
					"overlay": React.createElement(Tooltip, {
						"id": "statuslabel-status-3"
					}, __('Ship tag: %s', 'E4'))
				}, React.createElement(Label, {
					"bsStyle": "primary"
				}, React.createElement(FontAwesome, {
					"key": 0.,
					"name": 'tag'
				})));
			} else if ((this.props.label != null) && this.props.label === 4) {
				return React.createElement(OverlayTrigger, {
					"placement": "top",
					"overlay": React.createElement(Tooltip, {
						"id": "statuslabel-status-4"
					}, __('Ship tag: %s', '?'))
				}, React.createElement(Label, {
					"bsStyle": "success"
				}, React.createElement(FontAwesome, {
					"key": 0.,
					"name": 'tag'
				})));
			} else if ((this.props.label != null) && this.props.label === 5) {
				return React.createElement(OverlayTrigger, {
					"placement": "top",
					"overlay": React.createElement(Tooltip, {
						"id": "statuslabel-status-5"
					}, __('Ship tag: %s', '?'))
				}, React.createElement(Label, {
					"bsStyle": "warning"
				}, React.createElement(FontAwesome, {
					"key": 0.,
					"name": 'tag'
				})));
			} else if ((this.props.label != null) && this.props.label === 6) {
				return React.createElement(OverlayTrigger, {
					"placement": "top",
					"overlay": React.createElement(Tooltip, {
						"id": "statuslabel-status-6"
					}, __('Resupply needed'))
				}, React.createElement(Label, {
					"bsStyle": "warning"
				}, React.createElement(FontAwesome, {
					"key": 0.,
					"name": 'database'
				})));
			} else {
				return React.createElement(Label, {
					"bsStyle": "default",
					"style": {
						opacity: 0
					}
				});
			}
		}
	});

	return StatusLabel;

})();
