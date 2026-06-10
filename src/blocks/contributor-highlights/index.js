import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';

registerBlockType('contributor-highlights/profile', {
    apiVersion: 3,
    title: __('Contributor Highlights', 'contributor-highlights'),
    description: __('Display a WordPress.org contributor profile.', 'contributor-highlights'),
    icon: 'admin-users',
    category: 'widgets',
    keywords: ['contributor', 'profile', 'wordpress.org', 'badges'],
    supports: {
        html: false,
        align: ['wide', 'full'],
    },
    attributes: {
        username: {
            type: 'string',
            default: '',
        },
        compactVersion: {
            type: 'boolean',
            default: false,
        },
        showAvatar: {
            type: 'boolean',
            default: true,
        },
        showMeta: {
            type: 'boolean',
            default: true,
        },
        showCurrentJob: {
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
        showTeamFocus: {
            type: 'boolean',
            default: true,
        },
        showBadges: {
            type: 'boolean',
            default: true,
        },
        showReleases: {
            type: 'boolean',
            default: true,
        },
    },

    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps();
        const {
            username,
            compactVersion,
            showAvatar,
            showMeta,
            showCurrentJob,
            showBio,
            showContributions,
            showTeamFocus,
            showBadges,
            showReleases,
        } = attributes;

        const fullCardDisabled = compactVersion;

        return (
            <>
                <InspectorControls>
                    <PanelBody title={__('Profile Settings', 'contributor-highlights')}>
                        <TextControl
                            label={__('WordPress.org Username', 'contributor-highlights')}
                            value={username}
                            onChange={(value) => setAttributes({ username: value })}
                            help={__('Enter the WordPress.org username to display', 'contributor-highlights')}
                            __next40pxDefaultSize={true}
                            __nextHasNoMarginBottom={true}
                        />
                        <ToggleControl
                            label={__('Compact Version', 'contributor-highlights')}
                            checked={compactVersion}
                            onChange={() => setAttributes({ compactVersion: !compactVersion })}
                            help={__(
                                'Minimal card with avatar, meta, badges, and optional compact job or impact lines.',
                                'contributor-highlights'
                            )}
                            __nextHasNoMarginBottom={true}
                        />
                    </PanelBody>
                    <PanelBody title={__('Display Sections', 'contributor-highlights')} initialOpen={true}>
                        <ToggleControl
                            label={__('Show Avatar', 'contributor-highlights')}
                            checked={showAvatar}
                            onChange={() => setAttributes({ showAvatar: !showAvatar })}
                            __nextHasNoMarginBottom={true}
                        />
                        <ToggleControl
                            label={__('Show Profile Meta', 'contributor-highlights')}
                            checked={showMeta}
                            onChange={() => setAttributes({ showMeta: !showMeta })}
                            help={__(
                                'Handle, location, member since, links, teams, and languages.',
                                'contributor-highlights'
                            )}
                            __nextHasNoMarginBottom={true}
                        />
                        <ToggleControl
                            label={__('Show Current Job', 'contributor-highlights')}
                            checked={showCurrentJob}
                            onChange={() => setAttributes({ showCurrentJob: !showCurrentJob })}
                            __nextHasNoMarginBottom={true}
                        />
                        <ToggleControl
                            label={__('Show Bio', 'contributor-highlights')}
                            checked={showBio}
                            onChange={() => setAttributes({ showBio: !showBio })}
                            disabled={fullCardDisabled}
                            help={
                                fullCardDisabled
                                    ? __('Hidden in compact mode.', 'contributor-highlights')
                                    : undefined
                            }
                            __nextHasNoMarginBottom={true}
                        />
                        <ToggleControl
                            label={__('Show Recent Impact', 'contributor-highlights')}
                            checked={showContributions}
                            onChange={() => setAttributes({ showContributions: !showContributions })}
                            help={__(
                                '30, 90, and 12 month contribution stats. Also shown as one line in compact mode.',
                                'contributor-highlights'
                            )}
                            __nextHasNoMarginBottom={true}
                        />
                        <ToggleControl
                            label={__('Show Team Focus', 'contributor-highlights')}
                            checked={showTeamFocus}
                            onChange={() => setAttributes({ showTeamFocus: !showTeamFocus })}
                            disabled={fullCardDisabled}
                            help={
                                fullCardDisabled
                                    ? __('Full card only.', 'contributor-highlights')
                                    : __('365-day team contribution distribution.', 'contributor-highlights')
                            }
                            __nextHasNoMarginBottom={true}
                        />
                        <ToggleControl
                            label={__('Show Badges', 'contributor-highlights')}
                            checked={showBadges}
                            onChange={() => setAttributes({ showBadges: !showBadges })}
                            __nextHasNoMarginBottom={true}
                        />
                        <ToggleControl
                            label={__('Show WordPress Releases', 'contributor-highlights')}
                            checked={showReleases}
                            onChange={() => setAttributes({ showReleases: !showReleases })}
                            disabled={fullCardDisabled}
                            help={
                                fullCardDisabled
                                    ? __('Full card only.', 'contributor-highlights')
                                    : undefined
                            }
                            __nextHasNoMarginBottom={true}
                        />
                    </PanelBody>
                </InspectorControls>
                <div {...blockProps}>
                    {!username ? (
                        <p>{__('Please enter a WordPress.org username in the block settings.', 'contributor-highlights')}</p>
                    ) : (
                        <ServerSideRender
                            block="contributor-highlights/profile"
                            attributes={attributes}
                        />
                    )}
                </div>
            </>
        );
    },

    save: () => null,
});
