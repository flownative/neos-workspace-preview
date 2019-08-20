# Provides previews to Neos CMS workspaces

## Installation

`composer require flownative/workspace-preview`

## Link conversion in previews

In order to have correctly converted links in the workspace previews you
should use the overwritten `ConvertUrisImplementation`. To do that, add the
following to your Fusion:

```
prototype(Neos.Neos:ConvertUris) {
    @class = 'Flownative\\WorkspacePreview\\Fusion\\ConverUrisImplementation'
}
```

## Usage

After installation you will find an additional icon in the 
workspace module of your Neos installation. As Administrator
you will be able to create a preview link for an existing internal workspace.
Any editor can then use the second menu option to copy that link and give it to
people who should preview the workspace.
