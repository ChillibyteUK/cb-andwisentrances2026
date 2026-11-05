/**
 * Document-sidebar panel editing the post meta declared in inc/meta.php.
 *
 * The field list arrives through wp_localize_script() as window.cbaeMetaFields
 * ({ postType, fields: { key: { label, type, help, options, post_type } } }),
 * so adding a field is a one-place job in PHP. Values flow through
 * useEntityProp( 'postType', type, 'meta' ) — Gutenberg's own meta binding —
 * so they save with the post like any other attribute.
 *
 * Not a block (no block.json), so inc/blocks.php's glob ignores it;
 * inc/meta.php enqueues build/index.js on the relevant edit screens.
 */
import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/editor';
import { useEntityProp } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { TextControl, TextareaControl, SelectControl, ToggleControl, Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import MultiPostPicker from '../../_shared/MultiPostPicker';

const config = window.cbaeMetaFields || { postType: '', fields: {} };

function ImageField( { label, help, value, onChange } ) {
	const media = useSelect( ( select ) => ( value ? select( 'core' ).getMedia( value ) : null ), [ value ] );
	const url = media?.media_details?.sizes?.thumbnail?.source_url || media?.source_url;

	return (
		<MediaUploadCheck>
			<div style={ { marginBottom: '12px' } }>
				<p style={ { margin: '0 0 6px', fontSize: '11px', fontWeight: 500, textTransform: 'uppercase' } }>{ label }</p>
				{ url && <img src={ url } alt="" style={ { display: 'block', maxWidth: '120px', marginBottom: '6px', border: '1px solid #ddd' } } /> }
				<MediaUpload
					onSelect={ ( item ) => onChange( item.id ) }
					allowedTypes={ [ 'image' ] }
					value={ value }
					render={ ( { open } ) => (
						<div style={ { display: 'flex', gap: '6px' } }>
							<Button variant="secondary" size="small" onClick={ open }>
								{ value ? __( 'Replace', 'cb-andwisentrances2026' ) : __( 'Select', 'cb-andwisentrances2026' ) }
							</Button>
							{ value ? (
								<Button variant="tertiary" size="small" isDestructive onClick={ () => onChange( 0 ) }>
									{ __( 'Clear', 'cb-andwisentrances2026' ) }
								</Button>
							) : null }
						</div>
					) }
				/>
				{ help && <p style={ { margin: '6px 0 0', fontSize: '12px', color: '#757575' } }>{ help }</p> }
			</div>
		</MediaUploadCheck>
	);
}

function Field( { name, field, meta, setMeta } ) {
	const value = meta[ name ];
	const set = ( v ) => setMeta( { ...meta, [ name ]: v } );

	switch ( field.type ) {
		case 'ids':
			return <MultiPostPicker label={ field.label } help={ field.help } postTypes={ field.post_type } value={ value || [] } onChange={ set } />;
		case 'image':
			return <ImageField label={ field.label } help={ field.help } value={ value || 0 } onChange={ set } />;
		case 'boolean':
			return <ToggleControl label={ field.label } help={ field.help } checked={ !! value } onChange={ set } __nextHasNoMarginBottom />;
		case 'select':
			return (
				<SelectControl
					label={ field.label }
					help={ field.help }
					value={ value || field.default || '' }
					options={ Object.entries( field.options || {} ).map( ( [ v, l ] ) => ( { value: v, label: l } ) ) }
					onChange={ set }
					__nextHasNoMarginBottom
				/>
			);
		case 'integer':
			return <TextControl type="number" label={ field.label } help={ field.help } value={ value || '' } onChange={ ( v ) => set( v === '' ? 0 : Number( v ) ) } __nextHasNoMarginBottom />;
		case 'date':
			return <TextControl type="date" label={ field.label } help={ field.help } value={ value || '' } onChange={ set } __nextHasNoMarginBottom />;
		default:
			return field.multiline ? (
				<TextareaControl label={ field.label } help={ field.help } value={ value || '' } onChange={ set } rows={ 3 } __nextHasNoMarginBottom />
			) : (
				<TextControl label={ field.label } help={ field.help } value={ value || '' } onChange={ set } __nextHasNoMarginBottom />
			);
	}
}

function Panel() {
	const [ meta, setMeta ] = useEntityProp( 'postType', config.postType, 'meta' );
	const entries = Object.entries( config.fields );

	if ( ! entries.length || ! meta ) {
		return null;
	}

	return (
		<PluginDocumentSettingPanel name="cbae-meta" title={ __( 'Listing & details', 'cb-andwisentrances2026' ) } initialOpen>
			<div style={ { display: 'flex', flexDirection: 'column', gap: '14px' } }>
				{ entries.map( ( [ name, field ] ) => (
					<Field key={ name } name={ name } field={ field } meta={ meta } setMeta={ setMeta } />
				) ) }
			</div>
		</PluginDocumentSettingPanel>
	);
}

if ( config.postType ) {
	registerPlugin( 'cbae-meta-panel', { render: Panel } );
}
