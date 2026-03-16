# Provides previews to Neos CMS workspaces

## Installation

`composer require flownative/workspace-preview`

As a dependency, this package also installs [flownative/token-authentication](https://github.com/flownative/flow-token-auth). Please run `./flow doctrine:migrate` after installation to add the required tables for this package. 

## Link conversion in previews

In order to have correctly converted links in the workspace previews you
should use the overwritten `ConvertUrisImplementation`. To do that, add the
following to your Fusion:

```
prototype(Neos.Neos:ConvertUris) {
    @class = 'Flownative\\WorkspacePreview\\Fusion\\ConvertUrisImplementation'
}
```

## Usage

After installation you will find an additional icon in the
workspace module of your Neos installation. As Administrator
you will be able to create a preview link for an existing internal workspace.  
**Note:** If you get an error regarding permission issues, please run ``./flow session:destroyAll``

Any editor can then use the second menu option to copy that link and give it to
people who should preview the workspace.

The package also creates separate preview links for every pages in a workspace. These can be found by going to the metadata tab in the inspector and opening additional information, which now contains a button for the preview link.


## User-Interface Mode switcher
Some Neos websites render editor hints or other backend-specific features, depending on the user interface mode.
Due to Neos' preview logic, these also end up in the preview of this package.
To prevent this, the `userInterfaceModeSwitcher` can be enabled. This sets the UI mode (selectable in the backend) to the desktop preview in the frontend and thus prevents the display of backend-specific renderings.
Enabling the feature:
```yaml
Flownative:
  WorkspacePreview:
    userInterfaceModeSwitcher:
      enabled: true
```
To customize the mode used for preview:
```yaml
Flownative:
  WorkspacePreview:
    userInterfaceModeSwitcher:
      enabled: true
      previewMode: '<mode-name>'
```
