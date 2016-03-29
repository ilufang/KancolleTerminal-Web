var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

FontAwesome = React.createClass({

  displayName: 'FontAwesome',

  propTypes: {
    border: React.PropTypes.bool,
    className: React.PropTypes.string,
    fixedWidth: React.PropTypes.bool,
    flip: React.PropTypes.oneOf(['horizontal', 'vertical']),
    inverse: React.PropTypes.bool,
    name: React.PropTypes.string.isRequired,
    pulse: React.PropTypes.bool,
    rotate: React.PropTypes.oneOf([90, 180, 270]),
    size: React.PropTypes.oneOf(['lg', '2x', '3x', '4x', '5x']),
    spin: React.PropTypes.bool,
    stack: React.PropTypes.oneOf(['1x', '2x'])
  },

  render: function render() {
    var className = 'fa fa-' + this.props.name;

    if (this.props.size) {
      className += ' fa-' + this.props.size;
    }

    if (this.props.spin) {
      className += ' fa-spin';
    }

    if (this.props.pulse) {
      className += ' fa-pulse';
    }

    if (this.props.border) {
      className += ' fa-border';
    }

    if (this.props.fixedWidth) {
      className += ' fa-fw';
    }

    if (this.props.inverse) {
      className += ' fa-inverse';
    }

    if (this.props.flip) {
      className += ' fa-flip-' + this.props.flip;
    }

    if (this.props.rotate) {
      className += ' fa-rotate-' + this.props.rotate;
    }

    if (this.props.stack) {
      className += ' fa-stack-' + this.props.stack;
    }

    if (this.props.className) {
      className += ' ' + this.props.className;
    }

    return React.createElement('span', _extends({}, this.props, {
      className: className
    }));
  }
});
