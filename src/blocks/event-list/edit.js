import { useBlockProps } from '@wordpress/block-editor';
import { Disabled } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import './editor.scss';

export default function Edit( { attributes, name } ) {
	return (
		<div { ...useBlockProps() }>
			<Disabled>
				<ServerSideRender
					block={ name }
					attributes={ {
						// TODO: This value should come from the block controls in the block inspector.
						// TODO: This is only a proof of concept of the filter. The actual filter attribute will probably
						// 		 need a more complex structure, since we also need to be able to filter by "events of current user".
						filter: 'current',
					} }
				/>
			</Disabled>
		</div>
	);
}
