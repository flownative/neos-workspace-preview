import manifest from '@neos-project/neos-ui-extensibility';
import React from 'react';
import LinkView from './LinkView';


manifest('Flownative.WorkspacePreview:LinkView', {}, globalRegistry => {
	const viewsRegistry = globalRegistry.get('inspector').get('views');

	viewsRegistry.set('Flownative.WorkspacePreview/LinkView', {
		component: LinkView
	});
});
