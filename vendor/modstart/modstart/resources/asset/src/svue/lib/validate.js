import $ from 'jquery'

const Validate = function (value, option) {
  option = option || {}
  if (!('success' in option)) {
    option.success = function () {
    }
  }
  if (!('error' in option)) {
    option.error = function (msgList) {
    }
  }
  if (!('format' in option)) {
    option.format = 'string'
  }
  let msgList = []
  switch (option.format) {
    case 'string':
      value = $.trim(value)
      if ('require' in option) {
        if (!value) {
          msgList.push('字段为空')
        }
      }
      if ('type' in option) {
        switch (option.type) {
          case 'Aa1':
            if (!/^[A-Za-z0-9_][A-Za-z0-9_]*$/.test(value)) {
              msgList.push('只能包含数字字母下划线')
            }
            break;
        }
      }
      break;
    case 'integer':
    case 'float':
      switch (option.format) {
        case 'integer':
          value = parseInt(value)
          break;
        case 'float':
          value = parseFloat(value)
          break;
      }
      if (isNaN(value)) {
        msgList.push('字段不合法')
      } else {
        if ('min' in option) {
          if (value < option.min) {
            msgList.push('最小为' + option.min)
          }
        }
        if ('minX' in option) {
          if (value <= option.minX) {
            msgList.push('最小为' + option.minX)
          }
        }
        if ('max' in option) {
          if (value > option.max) {
            msgList.push('最大为' + option.max)
          }
        }
        if ('maxX' in option) {
          if (value >= option.maxX) {
            msgList.push('最大为' + option.maxX)
          }
        }
      }
      break;
  }
  if (msgList.length > 0) {
    option.error(msgList)
  } else {
    option.success(value)
  }
}
export {
  Validate
}
