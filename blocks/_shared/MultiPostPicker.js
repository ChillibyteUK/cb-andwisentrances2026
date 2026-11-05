import { useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { ComboboxControl, Button } from '@wordpress/components';
import { decodeEntities } from '@wordpress/html-entities';
import { __ } from '@wordpress/i18n';

/**
 * Ordered multi-post picker — the native-blocks equivalent of ACF's
 * `relationship` field. Search-as-you-type across one or more post types
 * (ComboboxControl, backed by @wordpress/core-data), with the chosen posts
 * listed underneath with move-up / move-down / remove controls. Order is
 * meaningful: it's the order the posts render in.
 *
 * Used by the Cards block ("selected posts" source) and by the document
 * sidebar panel for the related_* meta fields.
 *
 * @param {Object}   props
 * @param {string}   props.label     Field label.
 * @param {string[]} props.postTypes Post type slugs to search.
 * @param {number[]} props.value     Selected post IDs, in order.
 * @param {Function} props.onChange  ( ids: number[] ) => void
 * @param {string}   [props.help]    Help text.
 */
export default function MultiPostPicker( { label, postTypes, value, onChange, help } ) {
	const [ search, setSearch ] = useState( '' );
	const ids = Array.isArray( value ) ? value : [];
	const types = Array.isArray( postTypes ) ? postTypes : [ postTypes ];

	const results = useSelect(
		( select ) => {
			const { getEntityRecords } = select( coreStore );
			const out = [];
			types.forEach( ( type ) => {
				const records = getEntityRecords( 'postType', type, {
					search,
					status: 'publish',
					orderby: 'title',
					order: 'asc',
					per_page: 20,
					_fields: 'id,title,type',
				} );
				( records || [] ).forEach( ( record ) => out.push( record ) );
			} );
			return out;
		},
		[ search, types.join( ',' ) ]
	);

	const selected = useSelect(
		( select ) => {
			if ( ! ids.length ) {
				return [];
			}
			const { getEntityRecords } = select( coreStore );
			const out = [];
			types.forEach( ( type ) => {
				const records = getEntityRecords( 'postType', type, {
					include: ids,
					per_page: 100,
					_fields: 'id,title,type',
					status: 'any',
				} );
				( records || [] ).forEach( ( record ) => out.push( record ) );
			} );
			return out;
		},
		[ ids.join( ',' ), types.join( ',' ) ]
	);

	const titleOf = ( id ) => {
		const record = selected.find( ( post ) => post.id === id );
		return record ? decodeEntities( record.title?.rendered || '' ) || `#${ id }` : `#${ id }`;
	};

	const options = results
		.filter( ( post ) => ! ids.includes( post.id ) )
		.map( ( post ) => ( {
			value: String( post.id ),
			label: `${ decodeEntities( post.title?.rendered || '' ) || `#${ post.id }` }${ types.length > 1 ? ` (${ post.type })` : '' }`,
		} ) );

	function move( index, direction ) {
		const target = index + direction;
		if ( target < 0 || target >= ids.length ) {
			return;
		}
		const next = ids.slice();
		[ next[ index ], next[ target ] ] = [ next[ target ], next[ index ] ];
		onChange( next );
	}

	return (
		<div className="cb-andwisentrances2026-multi-post-picker">
			<ComboboxControl
				label={ label }
				value={ null }
				options={ options }
				onFilterValueChange={ ( input ) => setSearch( input ) }
				onChange={ ( id ) => {
					if ( id ) {
						onChange( [ ...ids, Number( id ) ] );
						setSearch( '' );
					}
				} }
				help={ help }
				placeholder={ __( 'Search to add…', 'cb-andwisentrances2026' ) }
				__nextHasNoMarginBottom
			/>
			{ ids.length > 0 && (
				<ol className="cb-andwisentrances2026-multi-post-picker__list">
					{ ids.map( ( id, index ) => (
						<li key={ id }>
							<span>{ titleOf( id ) }</span>
							<span className="cb-andwisentrances2026-multi-post-picker__actions">
								<Button size="small" label={ __( 'Move up', 'cb-andwisentrances2026' ) } onClick={ () => move( index, -1 ) } disabled={ 0 === index }>
									&#9650;
								</Button>
								<Button size="small" label={ __( 'Move down', 'cb-andwisentrances2026' ) } onClick={ () => move( index, 1 ) } disabled={ index === ids.length - 1 }>
									&#9660;
								</Button>
								<Button size="small" isDestructive label={ __( 'Remove', 'cb-andwisentrances2026' ) } onClick={ () => onChange( ids.filter( ( i ) => i !== id ) ) }>
									&times;
								</Button>
							</span>
						</li>
					) ) }
				</ol>
			) }
		</div>
	);
}
