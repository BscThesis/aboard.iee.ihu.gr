import React, { Component } from "react";
import Editor, { composeDecorators } from '@draft-js-plugins/editor';
import createImagePlugin from '@draft-js-plugins/image';
import createAlignmentPlugin from '@draft-js-plugins/alignment';
import createFocusPlugin from '@draft-js-plugins/focus';
import createResizeablePlugin from '@draft-js-plugins/resizeable';
import { convertFromRaw, EditorState } from 'draft-js';
import { convertToHTML, convertFromHTML as convertFromHTMLAlternative } from 'draft-convert';
import { AtomicBlockUtils } from "draft-js"

import { ContentState, convertFromHTML} from "draft-js"
import createToolbarPlugin, {
    Separator,
  } from '@draft-js-plugins/static-toolbar';
  import {
    ItalicButton,
    BoldButton,
    UnderlineButton,
    CodeButton,
    HeadlineOneButton,
    HeadlineTwoButton,
    HeadlineThreeButton,
    UnorderedListButton,
    OrderedListButton,
    BlockquoteButton,
    CodeBlockButton,
  } from '@draft-js-plugins/buttons';
  import editorStyles from './style_modules/editorStyles.module.css';
  import '@draft-js-plugins/static-toolbar/lib/plugin.css';
  import '@draft-js-plugins/image/lib/plugin.css';
  import '@draft-js-plugins/focus/lib/plugin.css';
  import '@draft-js-plugins/alignment/lib/plugin.css';


  class HeadlinesPicker extends Component {
    componentDidMount() {
      setTimeout(() => {
        window.addEventListener('click', this.onWindowClick);
      });
    }
  
    componentWillUnmount() {
      window.removeEventListener('click', this.onWindowClick);
    }
  
    onWindowClick = () =>
      // Call `onOverrideContent` again with `undefined`
      // so the toolbar can show its regular content again.
      this.props.onOverrideContent(undefined);
  
    render() {
      const buttons = [HeadlineOneButton, HeadlineTwoButton, HeadlineThreeButton];
      return (
        <div>
          {buttons.map((Button, i) => (
            // eslint-disable-next-line
            <Button key={i} {...this.props} />
          ))}
        </div>
      );
    }
  }
  
  class HeadlinesButton extends Component {
    onClick = () =>
      // A button can call `onOverrideContent` to replace the content
      // of the toolbar. This can be useful for displaying sub
      // menus or requesting additional information from the user.
      this.props.onOverrideContent(HeadlinesPicker);
  
    render() {
      return (
        <div className={editorStyles.headlineButtonWrapper}>
          <button onClick={this.onClick} className={editorStyles.headlineButton}>
            H
          </button>
        </div>
      );
    }
  }
  
  

    export default class CustomEditor extends Component {
        constructor(props) {
            super(props)
            const toolbarPlugin = createToolbarPlugin();
            const focusPlugin = createFocusPlugin();
            const resizeablePlugin = createResizeablePlugin();
            const alignmentPlugin = createAlignmentPlugin();
            // const { AlignmentTool } = alignmentPlugin;

            const decorator = composeDecorators(
            resizeablePlugin.decorator,
            alignmentPlugin.decorator,
            focusPlugin.decorator
            );

            const imagePlugin = createImagePlugin({ decorator });
            this.PluginComponents = {
                Toolbar: toolbarPlugin.Toolbar,
                AlignmentTool: alignmentPlugin.AlignmentTool
              };
            // const { Toolbar } = toolbarPlugin;
            this.plugins = [
                toolbarPlugin, 
                imagePlugin, 
                focusPlugin,
                alignmentPlugin,
                resizeablePlugin,
            ];

            this.state = {
                originalState: '',
                editorState: EditorState.createEmpty(),
            };
        }
        

        handlePastedFiles = ( files, editorState, stateFn) => {

            const FR = new FileReader();
            FR.addEventListener("load", (evt) => {
                const es = this.insertImage(evt.target.result, editorState)
                stateFn(es);
            }); 
                
            FR.readAsDataURL(files[0]);
        }

        isValidUrl = urlString => {
            var urlPattern = new RegExp('^(https?:\\/\\/)?'+ // validate protocol
          '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // validate domain name
          '((\\d{1,3}\\.){3}\\d{1,3}))'+ // validate OR ip (v4) address
          '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // validate port and path
          '(\\?[;&a-z\\d%_.~+=-]*)?'+ // validate query string
          '(\\#[-a-z\\d_]*)?$','i') // validate fragment locator
        return !!urlPattern.test(urlString)
      }
    
        convertUrlToBase64 = ( url, editorState, stateFn ) => {
            
            if (this.isValidUrl(url)) {
                this.isImgUrl(url).then(isImg => {
                    if (isImg) {
                        this.setBase64Image(url, this.setNewImage, editorState, stateFn)
                    }
                })
            }
            
        }
    
        setNewImage = (base64, editorState, stateFn) => {
            stateFn(this.insertImage(base64, editorState));
        }
    
        isImgUrl = (url) => {
            const img = new Image();
            img.src = url;
            return new Promise((resolve) => {
              img.onerror = () => resolve(false);
              img.onload = () => resolve(true);
            });
          }
    
        setBase64Image(url, callback, editorState, stateFn) {
            var xhr = new XMLHttpRequest();
            xhr.onload = function() {
                var reader = new FileReader();
                reader.onloadend = function() {
                    callback(reader.result, editorState, stateFn);
                }
                reader.readAsDataURL(xhr.response);
            };
            xhr.open('GET', url);
            xhr.responseType = 'blob';
            xhr.send();
        }
    
        insertImage = (url, editorState) => {
            const contentState = editorState.getCurrentContent();
            const contentStateWithEntity = contentState.createEntity(
                'IMAGE',
                'IMMUTABLE',
                { src: url },)
            const entityKey = contentStateWithEntity.getLastCreatedEntityKey();
            const newEditorState = EditorState.set( editorState, { currentContent: contentStateWithEntity });
            return AtomicBlockUtils.insertAtomicBlock(newEditorState, entityKey, ' ');
        };
      
        onChange = (editorState) => {
          this.setState({
            editorState,
          });
        };

        onBlur = (editorState) => {
            if (this.props.onChange && typeof this.props.onChange !== 'undefined') {
                const html = convertToHTML({
                    entityToHTML: (entity, originalText) => {
                        console.log(originalText, entity)
                      if (entity.type === 'IMAGE') {       
                        const width = isNaN(entity.data.width) ? entity.data.width : entity.data.width + "%"
                        return `<img src="${entity.data.src}" width="${entity.data.width}" style="width: ${width}" />`;
                      }
                      
                    //   return originalText;
                    },
                })(this.state.editorState.getCurrentContent());
    
                this.props.onChange(html)

                // console.log(html)
            }
            
        }
        customContentStateConverter = (contentState) => {
            // changes block type of images to 'atomic'
            const newBlockMap = contentState.getBlockMap().map((block) => {
                const entityKey = block.getEntityAt(0);
                if (entityKey !== null) {
                    const entityBlock = contentState.getEntity(entityKey);
                    const entityType = entityBlock.getType();
                    switch (entityType) {
                        case 'IMAGE': {
                            const newBlock = block.merge({
                                type: 'atomic',
                                text: ' '
                            });
                            return newBlock;
                        }
                        default:
                            return block;
                    }
                }
                return block;
            });
            const newContentState = contentState.set('blockMap', newBlockMap);
            return newContentState;
        }
        componentDidMount() {
            this.updateDraftRender(this.props.text)
        }

        componentDidUpdate(prevProps) {
            if (prevProps.text !== this.props.text) {
                if (this.props.text) {
                    this.updateDraftRender(this.props.text)
                }
            }
        }

        updateDraftRender(text) {
            // const contentState = convertFromHTMLAlternative({
            //     htmlToEntity: (nodeName, node, createEntity) => {
            //         if (nodeName === 'img') {
            //             return createEntity(
            //                 'IMAGE',
            //                 // 'atomic',
            //                 'IMMUTABLE',
            //                 {
            //                      src: node.src,
            //                      width: node.width
            //                 }
            //             )
            //         }
            //     }
                
            // })(this.props.text);

            const blocksFromHTML = convertFromHTML(text)
            const contentStateOriginal = ContentState.createFromBlockArray(
                blocksFromHTML.contentBlocks,
                blocksFromHTML.entityMap
            )

            // this.setState({
            //     editorState: EditorState.createWithContent(this.customContentStateConverter(
            //         contentStateOriginal
            //     )),
            // });
            // this.setState({
            //     editorState: EditorState.createWithContent(contentState),
            // });
            // const blocksFromHTML = convertFromHTML(this.props.text)
            this.setState({
                editorState: EditorState.createWithContent(this.customContentStateConverter(contentStateOriginal)),
            });
        }
      
        focus = () => {
          this.editor.focus();
        };

        handlePastedText = (text, styles, editorState) => {
            this.convertUrlToBase64(text, this.state.editorState, (editorState) => this.setState({editorState}))
        }
      
        render() {
          const { Toolbar, AlignmentTool } = this.PluginComponents;
          return (
            <div>
                <Toolbar>
                  {
                    // may be use React.Fragment instead of div to improve perfomance after React 16
                    (externalProps) => (
                      <div>
                        <BoldButton {...externalProps} />
                        <ItalicButton {...externalProps} />
                        <UnderlineButton {...externalProps} />
                        <CodeButton {...externalProps} />
                        <Separator {...externalProps} />
                        <HeadlinesButton {...externalProps} />
                        <UnorderedListButton {...externalProps} />
                        <OrderedListButton {...externalProps} />
                        <BlockquoteButton {...externalProps} />
                        <CodeBlockButton {...externalProps} />
                      </div>
                    )
                  }
                </Toolbar>
              <div className={editorStyles.editor} onClick={this.focus}>
                <Editor
                  editorState={this.state.editorState}
                  onChange={this.onChange}
                  onBlur={this.onBlur}
                  plugins={this.plugins}
                  ref={(element) => {
                    this.editor = element;
                  }}
                  handlePastedText={this.handlePastedText}
                  handlePastedFiles={files => this.handlePastedFiles(files, this.state.editorState, (editorState) => this.setState({editorState}))}  
                />
                
                {/* <AlignmentTool/> */}

              </div>
              
            </div>
          );
        }
      }