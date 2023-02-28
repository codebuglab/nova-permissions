<template>
    <default-field :field="field" :full-width-content="true">
        <template slot="field">
            <div class="permissionsCheckAll" @click="checkAll()">
                <label class="switchToggle">
                    <input type="checkbox" v-model="checkAllData" class="checkbox">
                    <span class="circle"></span>
                </label>
                <span>{{ checkAllData ? __('Clear Selection') : __('Select all')}}</span>
            </div>

            <div class="flex flex-wrap permissions" v-if="field.withGroups">
                <div
                        v-for="(permissions, group) in field.options"
                        :key="group"
                        class="mb-2 permissionsItem"
                >

                    <div class="cursor-pointer flex items-center px-2 py-2 bg-40 rounded-lg permissionsTitle">
                        <div class="w-full flex items-center">
                            <h3 class="capitalize flex-1">{{ fixNaming(group) }}</h3>
                            <div class="flex flex-wrap">
                                <div
                                        v-for="(permission, option) in permissions"
                                        :key="permission.option"
                                        class="pr-2"
                                >
                                    <span class="inline-block rounded-full w-2 h-2" :class="optionClass(permission.option)"></span>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="ml-auto">
                            <span class="font-bold text-xl" v-if="activeItem === group">&minus;</span>
                            <span class="font-bold text-xl" v-else>&plus;</span>
                        </div> -->
                    </div>

                    <div class="w-1/3 bg-20 px-2 py-2 border-l border-r border-b border-50 rounded-b-lg absolute z-50 permissionsContent">
                        <div
                                v-for="(permission, option) in permissions"
                                :key="permission.option"
                                class="px-1 py-1 items-center h permissionSwitch"
                        >
                            <checkbox
                                    :value="permission.option"
                                    :checked="isChecked(permission.option)"
                                    @input="toggleOption(permission.option)"
                                    class="pr-2"
                            ></checkbox>
                            <label
                                    :for="field.name"
                                    v-text="fixNaming(permission.label)"
                                    style="margin-top: 7px;display: inline-block;margin-bottom: 0;"
                                    @click="toggleOption(permission.option)"
                            ></label>
                        </div>
                    </div>
                </div>
            </div>
            <p
                    v-if="hasError"
                    class="my-2 text-danger"
            >{{ firstError }}</p>
        </template>
    </default-field>
</template>

<script>
    import {FormField, HandlesValidationErrors} from "laravel-nova";
    import VTooltip from 'v-tooltip'
    export default {
        component: [ VTooltip ],
        mixins: [FormField, HandlesValidationErrors],
        props: ["resourceName", "resourceId", "field"],
        data() {
            return {
                activeItem: true,
                checkAllData:false
            }
        },
        methods: {
            showItem(group) {
                if(!this.activeItem) {
                    this.activeItem = group
                }
                else if(this.activeItem !== group) {
                    this.activeItem = group
                }
                else {
                    this.activeItem = null
                }
            },
            checkAll() {
                this.checkAllData = !this.checkAllData
                if(this.checkAllData) {
                    // With Groups
                    if (this.field.withGroups) {
                        let permissions = _.flatMap(this.field.options);
                        for (var i = 0; i < permissions.length; i++) {
                            this.check(permissions[i].option);
                        }
                    }
                } else {
                    // With Groups
                    if (this.field.withGroups) {
                        let permissions = _.flatMap(this.field.options);
                        for (var i = 0; i < permissions.length; i++) {
                            this.uncheck(permissions[i].option);
                        }
                    }
                }
            },
            check(option) {
                if (!this.isChecked(option)) {
                    this.value.push(option);
                }
            },
            uncheck(option) {
                if (this.isChecked(option)) {
                    this.$set(this, "value", this.value.filter(item => item !== option));
                }
            },
            toggleOption(option) {
                if (this.isChecked(option)) {
                    return this.uncheck(option);
                }
                this.check(option);
            },
            /*
             * Set the initial, internal value for the field.
             */
            setInitialValue() {
                this.value = this.field.value || [];
            },
            /**
             * Fill the given FormData object with the field's internal value.
             */
            fill(formData) {
                formData.append(this.field.attribute, this.value || []);
            },
            /**
             * Update the field's internal value.
             */
            handleChange(value) {
                this.value = value;
            },
            groupName: (group) => {
                return group.replace('_', ' ')
            },
            fixNaming: (name) => {
                if(name.includes("_") === true) {
                    name = name.replace('_', ' ')
                }
                if(name.includes("-") === true) {
                    name = name.replace('-', ' ')
                }
                return name
            },
            isChecked(option) {
                return this.value ? this.value.includes(option) : false;
            },
            optionClass(option) {
                return {
                    'bg-success': this.value ? this.value.includes(option) : false,
                    'bg-danger': this.value ? !this.value.includes(option) : true,
                }
            },
        }
    };
</script>

<style>

.permissionsCheckAll
{
    font-weight:500;
    cursor:pointer;
    margin-bottom: 30px;
    display:inline-block;
}

.permissionsCheckAll .switchToggle 
{
    position: relative;
    top: -5px;
    left: 0;
    margin-left: 15px;
    pointer-events: none;
    margin-bottom: 0;
}

html[lang="en"] .permissionsCheckAll .switchToggle 
{
    margin-left:0;
    margin-right:15px;
}

.permissionsTitle
{
    background: var(--titlePermission);
    height: 68px;
    border-bottom-left-radius: 0px;
    border-bottom-right-radius: 0px;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
    padding:0 20px;
    cursor:default
}

.permissionsTitle .bg-danger
{
    background-color:#B7B7B7
}

.permissionsContent
{
    position:static;
    background-color:var(--grayFB);
    padding:0;
    border:none;
    width: 100%;   
    border-bottom-left-radius: 10px;
    border-bottom-right-radius: 10px;
    border-top-left-radius: 0px;
    border-top-right-radius: 0px;
    overflow:hidden;
}

.permissionsContent .permissionSwitch
{
    padding:15px;
    padding-left:80px;
    color:var(--colorNormalText);
    position:relative;
}

html[lang="en"] .permissionsContent .permissionSwitch
{
    padding-left:15px;
    padding-right:80px
}

.permissionsContent .permissionSwitch:nth-of-type(even)
{
    background-color:var(--gray5);
} 

.permissions
{
    margin:0 -7.5px
}

.permissionsItem
{
    width:50%;
    padding:0 7.5px;
}


@media(max-width:991px) {
    .permissions
    {
        margin:0
    }

    .permissionsItem
    {
        width:100%;
        padding:0;
    }
}


</style>