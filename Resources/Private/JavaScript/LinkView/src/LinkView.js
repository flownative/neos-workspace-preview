import React, {PureComponent} from 'react';
import PropTypes from 'prop-types';
import Clipboard from 'react-clipboard.js';
import {dataLoader} from '@neos-project/neos-ui-views';
import {$get} from "plow-js";

@dataLoader()
export default class LinkView extends PureComponent {
  static propTypes = {
    data: PropTypes.object.isRequired
  };

  render() {
    const linkUrl = $get(['link'], this.props.data);

    if (linkUrl === '---') {
      return (
        <div>---</div>
      )
    }

    return (
      <div>
        <Clipboard data-clipboard-text={linkUrl}>Copy Preview Link</Clipboard>
      </div>
    );
  }
}
