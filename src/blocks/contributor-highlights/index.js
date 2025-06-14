import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';

console.log('Contributor Highlights: Loading block script');

const blockName = 'contributor-highlights/profile';

const blockSettings = {
    apiVersion: 3,
    title: __('Contributor Highlights', 'contributor-highlights'),
    description: __('Display a WordPress.org contributor profile.', 'contributor-highlights'),
    icon: 'admin-users',
    category: 'widgets',
    supports: {
        html: false,
        align: ['wide', 'full'],
    },
    attributes: {
        username: {
            type: 'string',
            default: '',
        },
        showAvatar: {
            type: 'boolean',
            default: true,
        },
        showBio: {
            type: 'boolean',
            default: true,
        },
        showContributions: {
            type: 'boolean',
            default: true,
        },
        showBadges: {
            type: 'boolean',
            default: true,
        },
        showMeta: {
            type: 'boolean',
            default: true,
        },
    },

    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps();
        const {
            username,
            showAvatar,
            showBio,
            showContributions,
            showBadges,
            showMeta,
        } = attributes;

        return (
            <>
                <InspectorControls>
                    <PanelBody title={__('Profile Settings', 'contributor-highlights')}>
                        <TextControl
                            label={__('WordPress.org Username', 'contributor-highlights')}
                            value={username}
                            onChange={(value) => setAttributes({ username: value })}
                            help={__('Enter the WordPress.org username to display', 'contributor-highlights')}
                        />
                        <ToggleControl
                            label={__('Show Avatar', 'contributor-highlights')}
                            checked={showAvatar}
                            onChange={() => setAttributes({ showAvatar: !showAvatar })}
                        />
                        <ToggleControl
                            label={__('Show Bio', 'contributor-highlights')}
                            checked={showBio}
                            onChange={() => setAttributes({ showBio: !showBio })}
                        />
                        <ToggleControl
                            label={__('Show Contributions', 'contributor-highlights')}
                            checked={showContributions}
                            onChange={() => setAttributes({ showContributions: !showContributions })}
                        />
                        <ToggleControl
                            label={__('Show Badges', 'contributor-highlights')}
                            checked={showBadges}
                            onChange={() => setAttributes({ showBadges: !showBadges })}
                        />
                        <ToggleControl
                            label={__('Show Meta', 'contributor-highlights')}
                            checked={showMeta}
                            onChange={() => setAttributes({ showMeta: !showMeta })}
                        />
                    </PanelBody>
                </InspectorControls>
                <div {...blockProps}>
                    {!username ? (
                        <p>{__('Please enter a WordPress.org username in the block settings.', 'contributor-highlights')}</p>
                    ) : (
                        <ServerSideRender
                            block={blockName}
                            attributes={attributes}
                        />
                    )}
                </div>
            </>
        );
    },

    save: () => null, // Dynamic block, render on server
};

console.log('Contributor Highlights: Registering block', blockName);
registerBlockType(blockName, blockSettings);
console.log('Contributor Highlights: Block registered'); 