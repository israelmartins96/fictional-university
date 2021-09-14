wp.blocks.registerBlockType('custom-block/border-box', {
    title: 'Cool Border Box',
    icon: 'smiley',
    category: 'common',
    attributes: {
        content: { type: 'string' },
        colour: { type: 'string' }
    },
    edit: (props) => {
        const updateContent = (event) => {
            props.setAttributes({ content: event.target.value });
        };

        const updateColour = (value) => {
            props.setAttributes({ colour: value.hex });
        };
        
        return wp.element.createElement("div", null,
        /*#__PURE__*/wp.element.createElement("h3", null, "Cool Border Box"),
        /*#__PURE__*/wp.element.createElement("input", {
            type: "text", value: props.attributes.content, onChange: updateContent
        }),
        /*#__PURE__*/wp.element.createElement(wp.components.ColorPicker, {
            color: props.attributes.colour,
            onChangeComplete: updateColour
        }));;
    },
    save: (props) => {
        return wp.element.createElement("div", null, /*#__PURE__*/wp.element.createElement("h3", {
            style: {
                border: `5px solid ${props.attributes.colour}`
            }
        }, props.attributes.content));;
    }
});